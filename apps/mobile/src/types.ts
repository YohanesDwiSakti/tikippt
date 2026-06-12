export type Role = 'admin' | 'driver';

export type ApiEnvelope<T> = {
  data: T;
};

export type ApiErrorEnvelope = {
  error?: {
    code?: string;
    message?: string;
  };
};

export type Profile = {
  id: string;
  name: string;
  email: string;
  role: Role;
};

export type LoginResponse = {
  user: Profile;
  token: string;
};

export type TrackingEvent = {
  status: string;
  location: string;
  note?: string | null;
  created_at: string;
};

export type TrackingResult = {
  receipt: string;
  status: string;
  destination: string;
  latest_location: string;
  driver_name?: string | null;
  updated_at: string;
  delivery_proof?: {
    photo_url: string;
    delivered_at: string;
    delivered_location: string;
  } | null;
  timeline: TrackingEvent[];
};

export type DriverPackage = {
  receipt: string;
  destination: string;
  status: string;
  latest_location: string;
  assignment_status: string;
  assigned_at: string;
  admin_note?: string | null;
};

export type ProofPayload = {
  photo_url: string;
  delivered_at: string;
  delivered_location: string;
  latitude?: number | null;
  longitude?: number | null;
  note?: string | null;
};
