import type {
  ApiEnvelope,
  ApiErrorEnvelope,
  DriverPackage,
  LoginResponse,
  ProofPayload,
  TrackingResult,
} from '../types';

const fallbackBaseUrl = 'http://127.0.0.1:5000/api/v1';

export function apiBaseUrl(): string {
  return process.env.EXPO_PUBLIC_API_BASE_URL ?? fallbackBaseUrl;
}

async function request<T>(path: string, options: RequestInit = {}): Promise<T> {
  const response = await fetch(`${apiBaseUrl()}${path}`, {
    ...options,
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      ...(options.headers ?? {}),
    },
  });

  const body = (await response.json().catch(() => ({}))) as ApiEnvelope<T> & ApiErrorEnvelope;
  if (!response.ok) {
    throw new Error(body.error?.message ?? 'Request gagal. Coba lagi.');
  }

  return body.data;
}

export function trackReceipt(receipt: string): Promise<TrackingResult> {
  return request<TrackingResult>(`/tracking/${encodeURIComponent(receipt.trim().toUpperCase())}`);
}

export function loginDriver(email: string, password: string): Promise<LoginResponse> {
  return request<LoginResponse>('/auth/login', {
    method: 'POST',
    body: JSON.stringify({ email, password, role: 'driver' }),
  });
}

export function listDriverPackages(token: string): Promise<DriverPackage[]> {
  return request<DriverPackage[]>('/driver/packages', {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });
}

export function submitProof(token: string, receipt: string, payload: ProofPayload): Promise<unknown> {
  return request(`/driver/packages/${encodeURIComponent(receipt)}/proof`, {
    method: 'POST',
    headers: {
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify(payload),
  });
}
