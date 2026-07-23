export const APPLICATION_STATUS = {
  PENDING: 'pending',
  NEGATIVE: 'negative',
  POSITIVE: 'positive',
  INTERVIEW: 'interview',
  OFFER: 'offer',
  NO_RESPONSE: 'no_response',
} as const;

export const WORK_MODE = {
  REMOTE: 'remote',
  OFFICE: 'office',
  HYBRID: 'hybrid',
} as const;

export const COMPANY_SIZE = {
  SMALL: 'small',
  MEDIUM: 'medium',
  LARGE: 'large',
} as const;

export const PAGINATION = {
  DEFAULT_PAGE: 1,
  DEFAULT_PER_PAGE: 15,
  MAX_PER_PAGE: 100,
} as const;

export const API_CONFIG = {
  TIMEOUT: 30000,
  RETRY_ATTEMPTS: 3,
} as const;