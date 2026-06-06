const TONE_TO_VAR: Record<string, string> = {
  magenta: 'var(--magenta)',
  cobalt: 'var(--cobalt)',
  lime: 'var(--lime)',
  coral: 'var(--coral)',
  ink: 'var(--ink)',
  paper: 'var(--paper)',
};

export function toneVar(tone: string | undefined): string {
  return TONE_TO_VAR[tone || 'ink'] || 'var(--ink)';
}

export function toneFg(tone: string | undefined): string {
  return tone === 'lime' || tone === 'paper' ? 'var(--ink)' : 'var(--paper)';
}
