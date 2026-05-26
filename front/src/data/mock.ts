import type {
  Team, Match, Member, Pool, StandingRow,
} from '@/types';

// iso = flagcdn.com country code. Special: gb-eng = England, gb-sct = Scotland.
export const TEAMS: Record<string, Team> = {
  MEX: { name: 'México', code: 'MEX', iso: 'mx', group: 'A' },
  CAN: { name: 'Canadá', code: 'CAN', iso: 'ca', group: 'A' },
  USA: { name: 'EUA', code: 'USA', iso: 'us', group: 'B' },
  ENG: { name: 'Inglaterra', code: 'ENG', iso: 'gb-eng', group: 'B' },
  NED: { name: 'Países Baixos', code: 'NED', iso: 'nl', group: 'B' },
  IRN: { name: 'Irã', code: 'IRN', iso: 'ir', group: 'B' },
  BRA: { name: 'Brasil', code: 'BRA', iso: 'br', group: 'C' },
  CRO: { name: 'Croácia', code: 'CRO', iso: 'hr', group: 'C' },
  ECU: { name: 'Equador', code: 'ECU', iso: 'ec', group: 'C' },
  CMR: { name: 'Camarões', code: 'CMR', iso: 'cm', group: 'C' },
  FRA: { name: 'França', code: 'FRA', iso: 'fr', group: 'D' },
  BEL: { name: 'Bélgica', code: 'BEL', iso: 'be', group: 'D' },
  POL: { name: 'Polônia', code: 'POL', iso: 'pl', group: 'D' },
  TUN: { name: 'Tunísia', code: 'TUN', iso: 'tn', group: 'D' },
  ARG: { name: 'Argentina', code: 'ARG', iso: 'ar', group: 'E' },
  POR: { name: 'Portugal', code: 'POR', iso: 'pt', group: 'E' },
  URU: { name: 'Uruguai', code: 'URU', iso: 'uy', group: 'E' },
  KOR: { name: 'Coreia do Sul', code: 'KOR', iso: 'kr', group: 'E' },
  GER: { name: 'Alemanha', code: 'GER', iso: 'de', group: 'F' },
  ESP: { name: 'Espanha', code: 'ESP', iso: 'es', group: 'F' },
  JPN: { name: 'Japão', code: 'JPN', iso: 'jp', group: 'F' },
  MAR: { name: 'Marrocos', code: 'MAR', iso: 'ma', group: 'F' },
  ITA: { name: 'Itália', code: 'ITA', iso: 'it', group: 'G' },
  COL: { name: 'Colômbia', code: 'COL', iso: 'co', group: 'G' },
  SEN: { name: 'Senegal', code: 'SEN', iso: 'sn', group: 'G' },
  NZL: { name: 'Nova Zelândia', code: 'NZL', iso: 'nz', group: 'G' },
};

export const MEMBERS: Member[] = [
  { id: 1, name: 'Helena Vasques', handle: '@helena', avatar: 'HV', tone: 'magenta', role: 'OWNER' },
  { id: 2, name: 'Rafael Quintão', handle: '@rafa', avatar: 'RQ', tone: 'cobalt', role: 'ADMIN' },
  { id: 3, name: 'Você', handle: '@vc', avatar: 'VC', tone: 'lime', role: 'MEMBER', isMe: true },
  { id: 4, name: 'Bibi Tavares', handle: '@bibi', avatar: 'BT', tone: 'coral', role: 'MEMBER' },
  { id: 5, name: 'Téo Almeida', handle: '@teo', avatar: 'TA', tone: 'cobalt', role: 'MEMBER' },
  { id: 6, name: 'Caio Werneck', handle: '@caio', avatar: 'CW', tone: 'magenta', role: 'MEMBER' },
  { id: 7, name: 'Iara Pinheiro', handle: '@iara', avatar: 'IP', tone: 'lime', role: 'MEMBER' },
  { id: 8, name: 'Mateus Falcão', handle: '@falc', avatar: 'MF', tone: 'coral', role: 'MEMBER' },
  { id: 9, name: 'Joana Vidal', handle: '@joa', avatar: 'JV', tone: 'cobalt', role: 'MEMBER' },
  { id: 10, name: 'Pedro Coutinho', handle: '@petu', avatar: 'PC', tone: 'magenta', role: 'MEMBER' },
];

export const MATCHES: Match[] = [
  {
    id: 1, day: 'Qui 11/Jun', kickoff: '17:00',
    venue: 'Estádio Azteca · México',
    stage: 'GROUP_STAGE', group: 'A', status: 'FINISHED',
    home: 'MEX', away: 'CAN', homeScore: 2, awayScore: 1,
  },
  {
    id: 2, day: 'Sex 12/Jun', kickoff: '13:00',
    venue: 'SoFi Stadium · Inglewood',
    stage: 'GROUP_STAGE', group: 'B', status: 'FINISHED',
    home: 'USA', away: 'ENG', homeScore: 0, awayScore: 2,
  },
  {
    id: 3, day: 'Hoje · ao vivo', kickoff: "76'",
    venue: 'MetLife Stadium · East Rutherford',
    stage: 'GROUP_STAGE', group: 'C', status: 'IN_PROGRESS',
    home: 'BRA', away: 'CRO', homeScore: 2, awayScore: 1,
  },
  {
    id: 4, day: 'Amanhã', kickoff: '16:00',
    venue: 'Lumen Field · Seattle',
    stage: 'GROUP_STAGE', group: 'D', status: 'SCHEDULED',
    home: 'FRA', away: 'BEL',
  },
  {
    id: 5, day: 'Sáb 13/Jun', kickoff: '19:00',
    venue: 'Mercedes-Benz · Atlanta',
    stage: 'GROUP_STAGE', group: 'E', status: 'SCHEDULED',
    home: 'ARG', away: 'POR',
  },
  {
    id: 6, day: 'Dom 14/Jun', kickoff: '13:00',
    venue: 'Hard Rock · Miami',
    stage: 'GROUP_STAGE', group: 'F', status: 'SCHEDULED',
    home: 'GER', away: 'ESP',
  },
  {
    id: 7, day: 'Dom 14/Jun', kickoff: '17:30',
    venue: 'Gillette · Foxborough',
    stage: 'GROUP_STAGE', group: 'C', status: 'SCHEDULED',
    home: 'ECU', away: 'CMR',
  },
  {
    id: 8, day: 'Seg 15/Jun', kickoff: '14:00',
    venue: 'BMO Field · Toronto',
    stage: 'GROUP_STAGE', group: 'G', status: 'SCHEDULED',
    home: 'ITA', away: 'COL',
  },
];

export const POOLS: Pool[] = [
  {
    id: 'pool-resenha',
    name: 'Resenha do Bar',
    code: 'BARRES',
    members: 12,
    isPublic: false,
    myRank: 3,
    myPoints: 21,
    leader: 'Helena Vasques',
    leaderPoints: 28,
    accent: 'magenta',
  },
  {
    id: 'pool-trampo',
    name: 'Trampo FC',
    code: 'TRAMPO',
    members: 47,
    isPublic: false,
    myRank: 11,
    myPoints: 15,
    leader: 'Diretoria',
    leaderPoints: 32,
    accent: 'cobalt',
  },
  {
    id: 'pool-publico',
    name: 'Geral Brasil 🇧🇷',
    code: 'BRASIL',
    members: 8431,
    isPublic: true,
    myRank: 1281,
    myPoints: 18,
    leader: '@neto88',
    leaderPoints: 41,
    accent: 'lime',
  },
];

export const LEADERBOARD = [
  { memberId: 1, points: 28, exact: 4, result: 16, guesses: 22, trend: 'up' },
  { memberId: 2, points: 24, exact: 3, result: 15, guesses: 22, trend: 'same' },
  { memberId: 3, points: 21, exact: 3, result: 12, guesses: 22, trend: 'up' },
  { memberId: 4, points: 19, exact: 2, result: 13, guesses: 21, trend: 'down' },
  { memberId: 5, points: 18, exact: 2, result: 12, guesses: 22, trend: 'up' },
  { memberId: 6, points: 16, exact: 1, result: 13, guesses: 22, trend: 'down' },
  { memberId: 7, points: 14, exact: 1, result: 11, guesses: 20, trend: 'down' },
  { memberId: 8, points: 12, exact: 0, result: 12, guesses: 22, trend: 'same' },
  { memberId: 9, points: 9, exact: 0, result: 9, guesses: 18, trend: 'up' },
  { memberId: 10, points: 7, exact: 0, result: 7, guesses: 15, trend: 'down' },
];

export const GUESSES_M4 = [
  { memberId: 1, home: 2, away: 1 },
  { memberId: 2, home: 1, away: 1 },
  { memberId: 3, home: 2, away: 0 },
  { memberId: 4, home: 0, away: 2 },
  { memberId: 5, home: 3, away: 1 },
  { memberId: 6, home: 1, away: 2 },
  { memberId: 7, home: 2, away: 2 },
  { memberId: 8, home: 1, away: 0 },
];

export const STANDINGS_C: StandingRow[] = [
  { team: 'BRA', P: 2, V: 2, E: 0, D: 0, GP: 5, GC: 2, pts: 6 },
  { team: 'CRO', P: 2, V: 1, E: 0, D: 1, GP: 3, GC: 3, pts: 3 },
  { team: 'ECU', P: 2, V: 1, E: 0, D: 1, GP: 2, GC: 2, pts: 3 },
  { team: 'CMR', P: 2, V: 0, E: 0, D: 2, GP: 1, GC: 4, pts: 0 },
];

export function findPool(id: string | undefined): Pool {
  return POOLS.find(p => p.id === id) ?? POOLS[0];
}

export function findMatch(id: number | undefined): Match {
  return MATCHES.find(m => m.id === id) ?? MATCHES[2];
}

export function findMember(id: number): Member | undefined {
  return MEMBERS.find(m => m.id === id);
}

export const TONE_TO_VAR: Record<string, string> = {
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
