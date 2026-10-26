import express from "express";
import path from "path";
import dotenv from "dotenv";
import { createServer as createViteServer } from "vite";

dotenv.config();

const app = express();
const PORT = 3000;

app.use(express.json({ limit: "25mb" }));

const laravelApiBase = (process.env.LARAVEL_API_BASE_URL || "http://127.0.0.1:8085").replace(/\/$/, "");
const laravelPaths: Record<string, { method: string; path: string }> = {
  catalog: { method: "GET", path: "/api/mobile/catalog" },
  chat: { method: "POST", path: "/api/mobile/study-assistant/chat" },
  evaluate: { method: "POST", path: "/api/mobile/profile-evaluations" },
  enquiries: { method: "POST", path: "/api/mobile/enquiries" },
};

app.all(['/api/laravel/:resource', '/api/mobile/*'], async (req, res) => {
  const route = req.path.startsWith('/api/mobile/')
    ? Object.values(laravelPaths).find(item => item.path === req.path)
    : laravelPaths[req.params.resource];
  if (!route || req.method !== route.method) {
    return res.status(404).json({ message: "Unknown API route." });
  }

  try {
    const upstream = await fetch(`${laravelApiBase}${route.path}`, {
      method: route.method,
      headers: { Accept: "application/json", ...(route.method === "POST" ? { "Content-Type": "application/json" } : {}) },
      body: route.method === "POST" ? JSON.stringify(req.body) : undefined,
      signal: AbortSignal.timeout(25000),
    });
    const body = await upstream.text();
    res.status(upstream.status).type(upstream.headers.get("content-type") || "application/json").send(body);
  } catch (error) {
    console.error(`Laravel API ${route.path} failed:`, error);
    res.status(502).json({ message: "The GEIC data service is temporarily unavailable." });
  }
});

// Only public image assets are exposed; API URLs stay private to this server.
app.get(['/api/laravel-assets/*', '/assets/*', '/storage/*', '/landing/*'], async (req, res) => {
  const assetPath = req.path.startsWith('/api/laravel-assets/') ? req.params[0] : req.path.slice(1);
  if (!/^(assets|storage|landing)\//.test(assetPath) || assetPath.split('/').includes('..') ||
      !/\.(png|jpe?g|webp|gif|svg|avif)$/i.test(assetPath)) {
    return res.status(404).end();
  }
  try {
    const upstream = await fetch(`${laravelApiBase}/${assetPath}`, { signal: AbortSignal.timeout(10000) });
    if (!upstream.ok) return res.status(upstream.status).end();
    res.type(upstream.headers.get('content-type') || 'application/octet-stream');
    res.send(Buffer.from(await upstream.arrayBuffer()));
  } catch {
    res.status(502).end();
  }
});

// Retired ZIP demo endpoints never produce simulated results.
app.post('/api/ai-counsellor', (_req, res) => res.status(410).json({ message: 'Use the current study assistant.' }));

// Health check endpoint
app.get("/api/health", (_req, res) => {
  res.json({
    status: "ok",
    dataSource: "laravel",
    app: "GEIC Global - Trans Globe Indore Mobile",
    timestamp: Date.now(),
  });
});

const destinationSlug: Record<string, string> = {
  'United Kingdom': 'uk',
  'United States': 'usa',
  'Dubai & UAE': 'dubai',
};

app.post("/api/evaluate-profile", async (req, res) => {
  const profile = req.body;
  const levels: Record<string, string> = {
    Bachelors: 'Undergraduate',
    Masters: 'Postgraduate',
    Diploma: 'Diploma or pathway',
    Doctorate: 'Research',
  };
  const payload = {
    ...profile,
    academicPercentage: profile.scorePercentage,
    studyLevel: levels[profile.intendedLevel] || 'Postgraduate',
    originalStudyLevel: profile.intendedLevel,
    preferredDestinations: (profile.targetCountries || []).map((name: string) => destinationSlug[name] || name),
    englishTest: profile.englishTest === 'Not Taken Yet' ? 'Planning to take a test' : profile.englishTest,
    englishScore: profile.testScore,
  };

  try {
    const upstream = await fetch(`${laravelApiBase}/api/mobile/profile-evaluations`, {
      method: 'POST',
      headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
      signal: AbortSignal.timeout(25000),
    });
    const result: any = await upstream.json();
    if (!upstream.ok) return res.status(upstream.status).json(result);

    return res.json({
      overallScore: result.readinessScore,
      visaSuccessProbability: null,
      summary: result.summary,
      ambitiousMatches: ['Discuss ambitious university choices with a GEIC counsellor.'],
      targetMatches: result.matches.map((match: any) => `${match.name} - ${match.fit}`),
      safeMatches: ['No admission or visa outcome can be guaranteed.'],
      scholarshipEligibility: 'Confirm current institution-specific awards with a counsellor.',
      keyStrengths: result.strengths,
      recommendedActionItems: result.actionItems,
      disclaimer: result.disclaimer,
      submittedProfile: result.submittedProfile,
    });
  } catch (error) {
    console.error('Laravel evaluation failed:', error);
    return res.status(502).json({ message: 'Profile evaluation is temporarily unavailable.' });
  }
});

// Vite middleware & fallback for SPA
async function startServer() {
  if (process.env.NODE_ENV !== "production") {
    const vite = await createViteServer({
      server: { middlewareMode: true },
      appType: "spa",
    });
    app.use(vite.middlewares);
  } else {
    const distPath = path.join(process.cwd(), "dist");
    app.use(express.static(distPath));
    app.get("*", (_req, res) => {
      res.sendFile(path.join(distPath, "index.html"));
    });
  }

  app.listen(PORT, "0.0.0.0", () => {
    console.log(`Server running on port ${PORT}`);
  });
}

startServer();
