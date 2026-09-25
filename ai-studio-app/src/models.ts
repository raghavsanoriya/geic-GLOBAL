export type AppTab = 'home' | 'explore' | 'advisor' | 'evaluate' | 'more';

export type Destination = {
  id: string;
  name: string;
  tagline: string;
  detail: string;
  imageUrl: string;
  stat: string;
  highlights: string[];
  facts: string[][];
  costs: string[][];
  intakes: string[][];
  universities: string[];
};

export type Service = {
  id: string;
  title: string;
  subtitle: string;
  description: string;
  overview: string;
  imageUrl: string;
  highlights: string[];
  process: string[][];
};

export type Event = {
  id: string;
  title: string;
  type: string;
  date: string;
  time: string;
  location: string;
  destination: string;
  description: string;
  overview: string;
  imageUrl: string;
  highlights: string[];
};

export type TestPreparation = {
  id: string;
  title: string;
  subtitle: string;
  description: string;
  overview: string;
  imageUrl: string;
  facts: string[][];
  modules: string[][];
  note?: string | null;
};

export type Scholarship = {
  id: string;
  name: string;
  tagline: string;
  description: string;
  imageUrl: string;
  awards: string[][];
};

export type MobileCatalog = {
  destinations: Destination[];
  services: Service[];
  events: Event[];
  tests: TestPreparation[];
  scholarships: Scholarship[];
  contact: { office: string; email: string; website: string };
};

export type EvaluationResult = {
  readinessScore: number;
  summary: string;
  matches: Array<{ id: string; name: string; tagline: string; nextIntake: string; fit: string }>;
  strengths: string[];
  actionItems: string[];
  disclaimer: string;
};

export type ChatMessage = {
  id: string;
  role: 'user' | 'assistant';
  content: string;
};
