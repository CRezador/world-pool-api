/**
 * Placar do confronto com o desempate por pênaltis entre parênteses ao lado de
 * cada time, ex.: "1(4) × (5)1". Sem pênaltis, cai no formato simples "1 × 1".
 */
export function formatScoreWithPenalties(
  home: number | null | undefined,
  away: number | null | undefined,
  homePenalties?: number | null,
  awayPenalties?: number | null,
  separator = '×',
): string {
  const h = home ?? 0;
  const a = away ?? 0;

  if (homePenalties != null && awayPenalties != null) {
    return `${h}(${homePenalties}) ${separator} (${awayPenalties})${a}`;
  }

  return `${h} ${separator} ${a}`;
}
