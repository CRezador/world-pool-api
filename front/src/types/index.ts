export type Tone = 'magenta' | 'cobalt' | 'lime' | 'coral' | 'ink' | 'paper';

export type Stage =
  | 'GROUP_STAGE'
  | 'ROUND_OF_16'
  | 'QUARTER_FINALS'
  | 'SEMI_FINALS'
  | 'THIRD_PLACE'
  | 'FINAL';

export type MatchStatus = 'SCHEDULED' | 'IN_PROGRESS' | 'FINISHED';

export type MemberRole = 'OWNER' | 'ADMIN' | 'MEMBER';

export interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  created_at: string;
  updated_at: string;
};

export interface Team {
  name: string;
  code: string;
  iso: string;
  group: string;
}

export interface Match {
  id: number;
  day: string;
  kickoff: string;
  venue: string;
  stage: Stage;
  group: string;
  status: MatchStatus;
  home: string;
  away: string;
  homeScore?: number;
  awayScore?: number;
}

export interface Member {
  id: number;
  name: string;
  handle: string;
  avatar: string;
  tone: Tone;
  role: MemberRole;
  isMe?: boolean;
}

export interface Pool {
  id: string;
  name: string;
  code: string;
  members: number;
  isPublic: boolean;
  myRank: number;
  myPoints: number;
  leader: string;
  leaderPoints: number;
  accent: Tone;
}

export interface LeaderboardEntry {
  memberId: number;
  points: number;
  exact: number;
  result: number;
  guesses: number;
  trend: 'up' | 'down' | 'same';
}

export interface StandingRow {
  team: string;
  P: number;
  V: number;
  E: number;
  D: number;
  GP: number;
  GC: number;
  pts: number;
}
