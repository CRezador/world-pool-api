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
  id: number;
  name: string;
  code: string;
  flag_code: string;
  flag_url: string;
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
  home: Team | string;
  away: Team | string;
  homeScore?: number;
  awayScore?: number;
}

export interface ApiMatch {
  id: number;
  gameDay: number;
  kickoff: string | null;
  stage: Stage;
  group: string | null;
  status: MatchStatus;
  home: Team;
  away: Team;
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
  rank: number;
  userId: number;
  name: string;
  points: number;
  exactHits: number;
  resultHits: number;
  guessesCount: number;
  isMe: boolean;
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

export interface TeamStanding {
  position: number;
  team: string;
  code: string;
  crest: string;
  played: number;
  won: number;
  draw: number;
  lost: number;
  points: number;
}

export interface GroupStanding {
  group: string;
  table: TeamStanding[];
}
