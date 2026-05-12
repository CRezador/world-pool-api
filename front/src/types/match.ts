export type MatchStatus = 'SCHEDULED' | 'IN_PROGRESS' | 'FINISHED'
export type MatchStage =
  | 'GROUP_STAGE'
  | 'ROUND_OF_16'
  | 'QUARTER_FINALS'
  | 'SEMI_FINALS'
  | 'THIRD_PLACE'
  | 'FINAL'

export interface Match {
  id: number
  game_day: number
  home_team: string
  away_team: string
  stage: MatchStage
  group: string | null
  status: MatchStatus
  kickoff_at: string | null
  home_score: number | null
  away_score: number | null
}
