export interface StandingRow {
  position: number
  team: string
  crest: string
  played: number
  won: number
  draw: number
  lost: number
  goals_for: number
  goals_against: number
  goal_diff: number
  points: number
}

export interface Standing {
  group: string
  table: StandingRow[]
}
