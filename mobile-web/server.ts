import express from "express";
import path from "path";
import dotenv from "dotenv";
import { GoogleGenAI } from "@google/genai";
import { createServer as createViteServer } from "vite";

dotenv.config();

const app = express();
const PORT = 3000;

app.use(express.json({ limit: "25mb" }));

// Lazy initialization of Gemini client
function getGeminiClient(): GoogleGenAI | null {
  const apiKey = process.env.GEMINI_API_KEY;
  if (!apiKey) {
    return null;
  }
  return new GoogleGenAI({
    apiKey,
    httpOptions: {
      headers: {
        "User-Agent": "aistudio-build",
      },
    },
  });
}

// Health check endpoint
app.get("/api/health", (_req, res) => {
  res.json({
    status: "ok",
    hasApiKey: !!process.env.GEMINI_API_KEY,
    app: "GEIC Global - Trans Globe Indore Mobile",
    timestamp: Date.now(),
  });
});

// Endpoint: AI Counsellor (Trans Globe Study Assistant)
app.post("/api/ai-counsellor", async (req, res) => {
  try {
    const { message, conversationHistory = [] } = req.body;

    if (!message || typeof message !== "string" || message.trim().length === 0) {
      return res.status(400).json({ error: "Please provide a query message." });
    }

    const ai = getGeminiClient();

    const systemPrompt = `
You are the elite "Trans Globe Study Assistant", an expert AI study-abroad counsellor at Trans Globe Indore (managed by GEIC - Global Education & Immigration Consultants, established in 1992, with 70,250+ students placed, 800+ university tie-ups, and a 98.7% visa success rate).

Your goals:
1. Provide warm, highly accurate, authoritative, and encouraging guidance to students aspiring to study in Australia, the United Kingdom, Germany, Canada, the United States, Ireland, New Zealand, Dubai & UAE, Singapore, Switzerland, or Europe.
2. Address specific questions regarding:
   - Country comparisons (e.g. UK 1-yr masters vs Germany low tuition vs Australia Go8 PSW)
   - English language tests (IELTS, PTE, TOEFL, Duolingo) and minimum band requirements
   - Intake cycles (Fall/Spring/Winter) and application deadlines
   - Tuition costs, living expenses, and blocked accounts (like German Sperrkonto or Canadian GIC)
   - Post-Study Work Visas (e.g. 2-4 years in Australia, 2 years in UK Graduate Route, 18 months in Germany, up to 3 years PGWP in Canada, 3-year STEM OPT in USA)
   - Scholarships and financial aid
   - Visa documentation and financial planning
3. Mention that Trans Globe Indore provides 100% free expert counselling, SOP assistance, and visa filing with a 98.7% success rate at their Indore office (Phone: +91 98266 66886, Email: info@geic.in).
4. Keep answers formatted nicely for a mobile screen with crisp bullet points, clean spacing, and bold key terms.
5. Suggest 2-3 relevant follow-up action prompts at the bottom.
`;

    if (ai) {
      // Build conversation context
      const formattedHistory = conversationHistory
        .slice(-6)
        .map((m: any) => `${m.sender === "user" ? "Student" : "Counsellor"}: ${m.text}`)
        .join("\n\n");

      const prompt = `
${systemPrompt}

Prior Conversation:
${formattedHistory || "None"}

Student Question:
"${message.trim()}"

Provide a structured, helpful, and motivating answer tailored for a mobile app user.
`;

      const response = await ai.models.generateContent({
        model: "gemini-3.5-flash",
        contents: prompt,
      });

      const replyText = response.text || "I'd be delighted to help you explore your international education options.";

      return res.json({
        reply: replyText,
        source: "gemini-ai",
      });
    } else {
      // Intelligent fallback if API key is not supplied
      const lower = message.toLowerCase();
      let fallbackReply = "";

      if (lower.includes("germany") || lower.includes("german")) {
        fallbackReply = `🇩🇪 **Studying in Germany Overview**
• **Tuition Fees**: Virtually zero or nominal fees (€0 – €3,000/yr) at prestigious public universities like TU Munich, RWTH Aachen, and Heidelberg.
• **Living Expenses**: Blocked Account (Sperrkonto) of approx. €11,208/year required by German embassies.
• **Post-Study Work**: 18-month job search visa after graduation, with fast-track to EU Blue Card and Permanent Residency in 21–27 months.
• **Requirements**: Bachelor degree with 65%+ (APS certificate mandatory for Indian students), IELTS 6.5+ for English-taught degrees.

📍 Trans Globe Indore assists with APS document verification and German university shortlisting. Call **+91 98266 66886** to book a session!`;
      } else if (lower.includes("uk") || lower.includes("united kingdom") || lower.includes("london")) {
        fallbackReply = `🇬🇧 **Studying in the United Kingdom**
• **Program Structure**: 1-Year Masters degrees save a full year of living expenses and tuition fees.
• **Post-Study Work**: 2-Year Graduate Route visa allows you to work in any role in the UK without employer sponsorship.
• **Top Universities**: Russell Group (York, Manchester, Leeds) and London hubs like Brunel University London.
• **Intakes**: Major intake in September/October, with secondary intake in January/February.
• **Language Requirement**: IELTS 6.0–6.5 or PTE 58+. Several UK partners also accept 70%+ in 12th English!

Trans Globe has direct official tie-ups with leading UK universities and provides free application and CAS processing.`;
      } else if (lower.includes("australia") || lower.includes("melbourne") || lower.includes("sydney")) {
        fallbackReply = `🇦🇺 **Studying in Australia**
• **Group of Eight (Go8)**: World top-100 institutions like ANU, Monash, University of Queensland, and Adelaide.
• **Post-Study Work Rights**: 2 to 4 years post-study work visa depending on qualification and regional campus location.
• **Work While Studying**: Up to 48 hours per fortnight during semesters with high minimum hourly wages (AUD $23.23+/hr).
• **Intakes**: February/March (Primary) and July/August (Secondary).

Trans Globe Indore has guided thousands of students into Australian universities with a 98.7% visa success rate.`;
      } else if (lower.includes("ielts") || lower.includes("pte") || lower.includes("test")) {
        fallbackReply = `🗣️ **IELTS & PTE Test Preparation at GEIC Indore**
• **Certified Trainers**: British Council & IDP certified master faculty.
• **Format Options**: Available in both regular classroom sessions at our Indore center and live interactive online batches.
• **Mock Tests & AI Band Evaluation**: Weekly mock testing simulating real exam conditions with personalized band improvement roadmaps.
• **Target Scores**: Most undergraduate courses require IELTS 6.0 (min 5.5); Postgraduates require IELTS 6.5 (min 6.0) or PTE 58+.

Would you like to schedule a free diagnostic test at Trans Globe Indore?`;
      } else {
        fallbackReply = `🎓 **Welcome to Trans Globe Indore (GEIC Global)**
With over **34 years of legacy**, **800+ university tie-ups**, and a **98.7% visa success rate**, we guide students from first consultation to campus arrival.

**Our Core Services (Free for Students):**
1. **Unbiased University Shortlisting**: Australia, UK, Germany, Canada, USA, Ireland, Dubai & Europe.
2. **SOP & Application Editing**: Polished essays that stand out to admissions officers.
3. **Scholarship Guidance**: Helped students win over ₹25 Crores in scholarships.
4. **Visa Filing & Financial Checks**: Meticulous documentation review to avoid refusals.

Tell me your highest qualification, percentage, and dream study destination, and I will tailor recommendations for you!`;
      }

      return res.json({
        reply: fallbackReply,
        source: "expert-knowledge-base",
      });
    }
  } catch (error: any) {
    console.error("Error in /api/ai-counsellor:", error);
    res.status(500).json({
      error: error.message || "Failed to process counselling request",
    });
  }
});

// Endpoint: AI Profile Evaluation & Admissibility Matchmaker
app.post("/api/evaluate-profile", async (req, res) => {
  try {
    const profile = req.body;
    const {
      fullName = "Student",
      scorePercentage = 75,
      intendedLevel = "Masters",
      targetCountries = ["Australia", "United Kingdom"],
      intendedCourse = "Computer Science / Business",
      englishTest = "IELTS",
      testScore = "6.5",
      workExperienceYears = 0,
    } = profile;

    const ai = getGeminiClient();

    if (ai) {
      const prompt = `
You are a senior admissions committee advisor at Trans Globe Indore / GEIC Global.
Analyze the following student profile and generate a comprehensive evaluation in JSON:

Student: ${fullName}
Highest Score / GPA: ${scorePercentage}%
Target Degree: ${intendedLevel}
Target Countries: ${targetCountries.join(", ")}
Target Program: ${intendedCourse}
English Exam: ${englishTest} (Score: ${testScore || "Not yet taken"})
Work Experience: ${workExperienceYears} years

Calculate:
1. overallScore (integer 1-100)
2. visaSuccessProbability (number e.g. 98.4)
3. summary (concise 2-3 sentence overview of their candidacy)
4. ambitiousMatches (array of 2-3 top-tier ambitious universities)
5. targetMatches (array of 2-3 high-probability realistic target universities)
6. safeMatches (array of 2 safe universities with near 100% admit probability)
7. scholarshipEligibility (string describing scholarship amount e.g. "Eligible for 20% - 40% Merit Scholarships")
8. keyStrengths (array of 3 strings)
9. recommendedActionItems (array of 3 high-impact next steps)

Return strictly valid JSON matching this structure.
`;

      const response = await ai.models.generateContent({
        model: "gemini-3.5-flash",
        contents: prompt,
        config: {
          responseMimeType: "application/json",
        },
      });

      const parsed = JSON.parse(response.text || "{}");
      return res.json(parsed);
    } else {
      // Rule-based evaluation fallback
      const score = Math.min(96, Math.max(62, Math.round(scorePercentage * 0.95 + (workExperienceYears > 1 ? 6 : 0))));
      const visaProb = Number((95.5 + Math.min(3.7, scorePercentage * 0.04)).toFixed(1));

      return res.json({
        overallScore: score,
        visaSuccessProbability: visaProb,
        summary: `Strong academic candidacy for ${intendedLevel} in ${targetCountries.join(" & ")}. Your academic score of ${scorePercentage}% positions you well for recognized institutions with good post-study work pathways.`,
        ambitiousMatches: [
          "Monash University (Australia - Go8)",
          "University of York (UK - Russell Group)",
          "RWTH Aachen (Germany)",
        ],
        targetMatches: [
          "Queensland University of Technology (QUT)",
          "Brunel University London",
          "Auckland University of Technology (AUT)",
        ],
        safeMatches: [
          "Adelaide University Regional Campus",
          "Heriot-Watt University Dubai",
        ],
        scholarshipEligibility: scorePercentage >= 75 ? "Eligible for 15% – 35% Academic Merit Scholarships" : "Standard institutional bursaries applicable up to £3,000 / AUD $5,000",
        keyStrengths: [
          `Strong foundational percentage (${scorePercentage}%)`,
          `High demand for ${intendedCourse} across target destinations`,
          `Favorable post-study work visa regulations in chosen countries`,
        ],
        recommendedActionItems: [
          "Finalize Statement of Purpose (SOP) with GEIC Indore editorial team",
          `Complete ${englishTest} target score verification`,
          "Submit applications before the priority scholarship deadline",
        ],
      });
    }
  } catch (error: any) {
    console.error("Error in /api/evaluate-profile:", error);
    res.status(500).json({
      error: error.message || "Failed to evaluate student profile",
    });
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
