export interface LeaderboardEntry {
  rank: number
  user: {
    id: number
    name: string
  }
  points: number
  exact_hits: number
  result_hits: number
  guesses_count: number
  updated_at: string | null
}
