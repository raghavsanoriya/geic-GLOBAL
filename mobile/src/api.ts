import { Platform } from 'react-native';
import type { CatalogData, EnquiryDraft, EvaluationRequest, EvaluationResult } from './types';

const API_BASE_URL = (process.env.EXPO_PUBLIC_API_BASE_URL ?? 'https://geic.in/api/mobile').replace(/\/$/, '');

type ApiErrorBody = { message?: string; errors?: Record<string, string[]> };

export class ApiError extends Error {
  constructor(message: string, public readonly status?: number, public readonly errors?: Record<string, string[]>) {
    super(message);
  }
}

async function requestJson<T>(path: string, init?: RequestInit): Promise<T> {
  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), 15000);

  try {
    const response = await fetch(`${API_BASE_URL}${path}`, {
      ...init,
      headers: { Accept: 'application/json', 'Content-Type': 'application/json', ...init?.headers },
      signal: controller.signal,
    });
    const body = (await response.json().catch(() => ({}))) as T & ApiErrorBody;

    if (!response.ok) {
      throw new ApiError(body.message ?? 'The request could not be completed.', response.status, body.errors);
    }

    return body;
  } catch (error) {
    if (error instanceof ApiError) throw error;
    throw new ApiError(error instanceof Error && error.name === 'AbortError' ? 'The request timed out. Please try again.' : 'You appear to be offline. Please try again.');
  } finally {
    clearTimeout(timeout);
  }
}

export async function fetchCatalog(): Promise<CatalogData> {
  const response = await requestJson<{ data: CatalogData }>('/catalog');
  return response.data;
}

export function sendChat(message: string, history: { role: 'user' | 'assistant'; content: string }[]) {
  return requestJson<{ reply: string; source: 'assistant' | 'guided' }>('/study-assistant/chat', {
    method: 'POST',
    body: JSON.stringify({ message, history }),
  });
}

export function evaluateProfile(payload: EvaluationRequest) {
  return requestJson<EvaluationResult>('/profile-evaluations', { method: 'POST', body: JSON.stringify(payload) });
}

export function submitEnquiry(
  draft: EnquiryDraft,
  context: { kind: 'counselling' | 'event' | 'service' | 'expo'; referenceId?: string },
) {
  return requestJson<{ success: true; message: string; reference: string }>('/enquiries', {
    method: 'POST',
    body: JSON.stringify({
      kind: context.kind,
      ...draft,
      referenceId: context.referenceId,
      platform: Platform.OS,
      website: '',
    }),
  });
}
