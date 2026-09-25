export interface StarterPrompt {
  label: string;
  category: string;
  idea: string;
  suggestedTone: 'professional' | 'witty' | 'urgent' | 'inspirational';
}

export const STARTER_PROMPTS: StarterPrompt[] = [
  {
    label: "Product Launch",
    category: "Startup",
    idea: "Launching an AI-assisted workspace that turns meeting transcripts into automated Jira tickets and executive summaries in under 30 seconds.",
    suggestedTone: "urgent",
  },
  {
    label: "Career Lesson",
    category: "Leadership",
    idea: "Why the highest performing engineers aren't the ones who write the most code, but the ones who know which 80% of code never needs to be written.",
    suggestedTone: "professional",
  },
  {
    label: "Industry Hot Take",
    category: "Tech",
    idea: "Most companies building 'AI agents' are just wrapping an if/else loop in prompt engineering. Here is what an actual autonomous reasoning system looks like.",
    suggestedTone: "witty",
  },
  {
    label: "Flash Sale / Event",
    category: "Commerce",
    idea: "Announcing our 48-hour summer design sprint summit with keynote speakers from Figma, Linear, and Vercel. 100 early-bird passes released right now.",
    suggestedTone: "urgent",
  },
  {
    label: "Creator Mindset",
    category: "Growth",
    idea: "The 3 habits that helped me publish 150 weeks of weekly essays without missing a single Monday deadline, even while working a full-time product job.",
    suggestedTone: "inspirational",
  },
];
