import { applyCatalog, type StudioCatalog } from '../data/liveCatalog';
export async function apiRequest<T>(resource: string, payload?: unknown): Promise<T> {
  const paths: Record<string, string> = { catalog: 'catalog', chat: 'study-assistant/chat', evaluate: 'profile-evaluations', enquiries: 'enquiries' };
  if (!paths[resource]) throw new Error('Unknown data request');
  const response = await fetch(`/api/mobile/${paths[resource]}`, {
    method: payload === undefined ? 'GET' : 'POST',
    headers: { Accept: 'application/json', ...(payload === undefined ? {} : { 'Content-Type': 'application/json' }) },
    body: payload === undefined ? undefined : JSON.stringify(payload),
    signal: AbortSignal.timeout(30000),
  });
  const data = await response.json().catch(() => { throw new Error('The data service returned an invalid response. Please try again.'); });
  if (!response.ok) {
    throw new Error(data.message || `GEIC API request failed (${response.status})`);
  }
  return data as T;
}

type EnquiryInput = {
  kind: 'counselling' | 'event' | 'service' | 'expo';
  fullName: string;
  phone: string;
  email?: string;
  destination?: string;
  studyLevel?: string;
  appointmentDate?: string;
  timeSlot?: string;
  meetingMode?: string;
  referenceId?: string;
  message?: string;
};

export async function submitEnquiry(input: EnquiryInput): Promise<{ reference: string }> {
  const platform = /Android/i.test(navigator.userAgent) ? 'android'
    : /iPhone|iPad/i.test(navigator.userAgent) ? 'ios' : 'web';
  const result = await apiRequest<{ reference: string }>('enquiries', {
    ...input,
    email: input.email?.trim() || `no-email-${Date.now()}@example.invalid`,
    emailProvided: Boolean(input.email?.trim()),
    platform,
    consent: true,
  });
  if (!result.reference) throw new Error('Your request was not confirmed. Please contact the team.');
  return result;
}


export async function syncLaravelCatalog(): Promise<void> {
  const response = await apiRequest<{ studio: StudioCatalog }>('catalog');
  applyCatalog(response.studio);
}
