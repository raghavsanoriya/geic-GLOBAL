import { Platform } from 'react-native';
import type { ChatMessage, EvaluationResult, MobileCatalog } from './models';

const laravelOrigin =
  process.env.EXPO_PUBLIC_LARAVEL_URL ??
  (Platform.OS === 'android' ? 'http://10.0.2.2:8085' : 'http://127.0.0.1:8085');

const apiBaseUrl = `${laravelOrigin.replace(/\/$/, '')}/api/mobile`;

async function request<T>(path: string, init?: RequestInit): Promise<T> {
  const response = await fetch(`${apiBaseUrl}${path}`, {
    ...init,
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      ...init?.headers,
    },
  });
  const body = await response.json().catch(() => ({}));

  if (!response.ok) {
    const message = typeof body.message === 'string' ? body.message : 'The GEIC service is unavailable. Please try again.';
    throw new Error(message);
  }

  return body as T;
}

export async function loadCatalog(): Promise<MobileCatalog> {
  const response = await request<{ data: MobileCatalog }>('/catalog');
  return response.data;
}

export async function askStudyAssistant(message: string, history: ChatMessage[]): Promise<string> {
  const response = await request<{ reply: string }>('/study-assistant/chat', {
    method: 'POST',
    body: JSON.stringify({
      message,
      history: history.slice(-10).map(({ role, content }) => ({ role, content })),
    }),
  });
  return response.reply;
}

export async function evaluateProfile(input: {
  academicPercentage: number;
  studyLevel: string;
  preferredDestinations: string[];
  englishTest: string;
  englishScore?: string;
  preferredIntake?: string;
}): Promise<EvaluationResult> {
  return request<EvaluationResult>('/profile-evaluations', {
    method: 'POST',
    body: JSON.stringify(input),
  });
}

export async function submitEnquiry(input: {
  fullName: string;
  email: string;
  phone: string;
  city?: string;
  destination?: string;
  studyLevel?: string;
  preferredIntake?: string;
  preferredCourse?: string;
  englishTest?: string;
  message?: string;
  referenceId?: string;
}): Promise<{ reference: string; message: string }> {
  return request<{ reference: string; message: string }>('/enquiries', {
    method: 'POST',
    body: JSON.stringify({
      ...input,
      kind: 'counselling',
      platform: Platform.OS === 'ios' ? 'ios' : Platform.OS === 'android' ? 'android' : 'web',
      consent: true,
    }),
  });
}
