export type Tone = 'magenta' | 'cobalt' | 'lime' | 'coral' | 'ink' | 'paper';

export type Stage =
  | 'GROUP_STAGE'
  | 'SECOND_ROUND'
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
  group?: string;
  status: MatchStatus;
  home?: Team | string;
  away?: Team | string;
  homeScore?: number;
  awayScore?: number;
  /** Knockout tie label, e.g. "1C × 2E" or "A GRANDE DECISÃO". */
  tie?: string;
  /** Bracket slot labels shown while the teams are still undefined (TBD). */
  homeSlot?: string;
  awaySlot?: string;
  date?: string;
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
  isMember: boolean;
  myRank: number;
  myPoints: number;
  leader: string;
  leaderPoints: number;
  lastResults: number[];
  accent: Tone;
}

export interface GuessEntry {
  id: number;
  matchId: number;
  homeScore: number;
  awayScore: number;
  points: number | null;
  match: {
    id: number;
    stage: string;
    group: string | null;
    status: 'SCHEDULED' | 'IN_PROGRESS' | 'FINISHED';
    kickoffAt: string | null;
    homeScore: number | null;
    awayScore: number | null;
    homeTeam: { code: string; flagUrl: string | null };
    awayTeam: { code: string; flagUrl: string | null };
  };
}

export interface LeaderboardEntry {
  rank: number;
  previousRank: number | null;
  trend: 'up' | 'down' | 'equal';
  userId: number;
  name: string;
  role: 'OWNER' | 'ADMIN' | 'MEMBER';
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

/** One row in a group's standings table (used by the Jogos "Grupos" view). */
export interface GroupTableRow {
  code: string;
  name: string;
  iso: string;
  P: number;
  V: number;
  E: number;
  D: number;
  GP: number;
  GC: number;
  pts: number;
  form: string[];
}

/** A full group (letter + its 4 teams) for the Jogos group grid / drill-down. */
export interface GroupFull {
  id: number;
  g: string;
  rows: GroupTableRow[];
}

/** A knockout phase definition for the Jogos "Mata-mata" view. */
export interface KnockoutStage {
  id: Stage;
  label: string;
  short: string;
  accent: Tone;
  note: string;
}

export interface PlayerProfile {
  name: string;
  handle: string;
  avatar: string;
  tone: Tone;
  seasonPoints: number;
  hitRate: number;
  streak: number;
  bestRank: number;
  bestPool: string;
  totalGuesses: number;
  exactCount: number;
}

export interface GuessHistoryEntry {
  matchId: number;
  home: string;
  away: string;
  myHome: number;
  myAway: number;
  realHome: number;
  realAway: number;
  pts: number;
  status: 'scored' | 'pending';
  pool: string;
  date: string;
}

export type ActivityAction = 'cravou' | 'acertou' | 'errou' | 'palpitou';

export interface ActivityItem {
  id: number;
  poolId: number;
  poolName: string;
  createdAt: string;
  actor: string;
  isMe: boolean;
  action: ActivityAction;
  subject: string;
  points: number | null;
}
