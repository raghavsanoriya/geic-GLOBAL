import type { ImageSourcePropType } from 'react-native';
import type { NavigatorScreenParams } from '@react-navigation/native';

export type Destination = {
  id: string;
  name: string;
  tagline: string;
  detail: string;
  image?: ImageSourcePropType;
  imageUrl?: string;
  stat: string;
  highlights: string[];
  facts?: string[][];
  costs?: string[][];
  intakes?: string[][];
  universities?: string[];
};

export type Service = {
  id: string;
  title: string;
  subtitle: string;
  description: string;
  overview?: string;
  image?: ImageSourcePropType;
  imageUrl?: string;
  icon: string;
  highlights?: string[];
  process?: string[][];
};

export type EventItem = {
  id: string;
  title: string;
  type: string;
  date: string;
  time: string;
  location: string;
  destination?: string;
  description: string;
  overview?: string;
  image?: ImageSourcePropType;
  imageUrl?: string;
  highlights?: string[];
};

export type TestItem = {
  id: string;
  title: string;
  subtitle: string;
  description: string;
  overview?: string;
  image?: ImageSourcePropType;
  imageUrl?: string;
  facts?: string[][];
  modules?: string[][];
  note?: string | null;
};

export type Scholarship = {
  id: string;
  name: string;
  tagline: string;
  description: string;
  image?: ImageSourcePropType;
  imageUrl?: string;
  awards?: string[][];
};

export type CatalogData = {
  destinations: Destination[];
  services: Service[];
  events: EventItem[];
  tests: TestItem[];
  scholarships: Scholarship[];
  testimonials: { id: string; name: string; quote: string; detail?: string }[];
  contact: { office: string; email: string; website: string };
};

export type ChatMessage = { id: string; role: 'user' | 'assistant'; content: string };

export type EvaluationRequest = {
  academicPercentage: number;
  studyLevel: string;
  preferredDestinations: string[];
  englishTest: string;
  englishScore?: string;
  preferredIntake?: string;
};

export type EvaluationResult = {
  readinessScore: number;
  summary: string;
  matches: { id: string; name: string; tagline: string; nextIntake: string; fit: string }[];
  strengths: string[];
  actionItems: string[];
  disclaimer: string;
};

export type EnquiryDraft = {
  fullName: string;
  email: string;
  phone: string;
  city: string;
  destination: string;
  studyLevel: string;
  preferredIntake: string;
  preferredCourse: string;
  englishTest: string;
  message: string;
  consent: boolean;
};

export type RootStackParamList = {
  MainTabs: NavigatorScreenParams<RootTabParamList> | undefined;
  Destination: { id: string };
  Service: { id: string };
  Event: { id: string };
  Booking: { kind?: 'counselling' | 'event' | 'service' | 'expo'; referenceId?: string; title?: string } | undefined;
  Notifications: undefined;
};

export type RootTabParamList = {
  Home: undefined;
  Explore: undefined;
  Advisor: undefined;
  Evaluate: undefined;
  More: undefined;
};
