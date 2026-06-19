import type {
  Tone, Team, Match, Member, Pool, StandingRow, ActivityItem,
  PlayerProfile, GroupFull, KnockoutStage,
} from '@/types';

// iso = flagcdn.com country code. Special: gb-eng = England, gb-sct = Scotland.
const RAW_TEAMS: Record<string, { name: string; code: string; iso: string; group: string }> = {
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
  SUI: { name: 'Suíça', code: 'SUI', iso: 'ch', group: 'A' },
  AUT: { name: 'Áustria', code: 'AUT', iso: 'at', group: 'H' },
  CRC: { name: 'Costa Rica', code: 'CRC', iso: 'cr', group: 'H' },
  KSA: { name: 'Arábia Saudita', code: 'KSA', iso: 'sa', group: 'H' },
  SCO: { name: 'Escócia', code: 'SCO', iso: 'gb-sct', group: 'I' },
  AUS: { name: 'Austrália', code: 'AUS', iso: 'au', group: 'I' },
  PAR: { name: 'Paraguai', code: 'PAR', iso: 'py', group: 'I' },
  CIV: { name: 'Costa do Marfim', code: 'CIV', iso: 'ci', group: 'J' },
  QAT: { name: 'Catar', code: 'QAT', iso: 'qa', group: 'J' },
  DEN: { name: 'Dinamarca', code: 'DEN', iso: 'dk', group: 'K' },
  SRB: { name: 'Sérvia', code: 'SRB', iso: 'rs', group: 'K' },
  CHI: { name: 'Chile', code: 'CHI', iso: 'cl', group: 'K' },
  GHA: { name: 'Gana', code: 'GHA', iso: 'gh', group: 'K' },
  NOR: { name: 'Noruega', code: 'NOR', iso: 'no', group: 'L' },
  PAN: { name: 'Panamá', code: 'PAN', iso: 'pa', group: 'L' },
  EGY: { name: 'Egito', code: 'EGY', iso: 'eg', group: 'L' },
};

export const TEAMS: Record<string, Team> = Object.fromEntries(
  Object.entries(RAW_TEAMS).map(([key, t], i) => [key, {
    id: i + 1,
    name: t.name,
    code: t.code,
    flag_code: t.iso,
    flag_url: `https://flagcdn.com/40x30/${t.iso}.png`,
    group: t.group,
  } satisfies Team]),
);

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
  {
    id: 9, day: 'Sex 12/Jun', kickoff: '19:00',
    venue: 'Levi\'s · Santa Clara',
    stage: 'GROUP_STAGE', group: 'D', status: 'SCHEDULED',
    home: 'POL', away: 'TUN',
  },
  {
    id: 10, day: 'Sáb 13/Jun', kickoff: '13:00',
    venue: 'NRG · Houston',
    stage: 'GROUP_STAGE', group: 'E', status: 'SCHEDULED',
    home: 'URU', away: 'KOR',
  },
  {
    id: 11, day: 'Dom 14/Jun', kickoff: '16:00',
    venue: 'AT&T · Arlington',
    stage: 'GROUP_STAGE', group: 'F', status: 'SCHEDULED',
    home: 'JPN', away: 'MAR',
  },
  {
    id: 12, day: 'Seg 15/Jun', kickoff: '17:30',
    venue: 'Arrowhead · Kansas City',
    stage: 'GROUP_STAGE', group: 'G', status: 'SCHEDULED',
    home: 'SEN', away: 'NZL',
  },

  // ── MATA-MATA · SEGUNDA FASE (Round of 32) ──
  {
    id: 21, day: 'Ter 23/Jun', kickoff: '13:00', date: '23/06/2026',
    venue: 'SoFi Stadium · Inglewood',
    stage: 'SECOND_ROUND', tie: '1A × 3CDF', status: 'SCHEDULED',
    home: 'MEX', away: 'ECU',
  },
  {
    id: 22, day: 'Ter 23/Jun', kickoff: '17:00', date: '23/06/2026',
    venue: 'MetLife · East Rutherford',
    stage: 'SECOND_ROUND', tie: '1C × 2E', status: 'SCHEDULED',
    home: 'BRA', away: 'POR',
  },
  {
    id: 23, day: 'Qua 24/Jun', kickoff: '13:00', date: '24/06/2026',
    venue: 'AT&T · Arlington',
    stage: 'SECOND_ROUND', tie: '1E × 2C', status: 'SCHEDULED',
    home: 'ARG', away: 'CRO',
  },
  {
    id: 24, day: 'Qua 24/Jun', kickoff: '17:00', date: '24/06/2026',
    venue: 'Hard Rock · Miami',
    stage: 'SECOND_ROUND', tie: '1F × 2B', status: 'SCHEDULED',
    home: 'ESP', away: 'NED',
  },

  // ── MATA-MATA · OITAVAS (Round of 16) ──
  {
    id: 13, day: 'Sáb 28/Jun', kickoff: '13:00', date: '28/06/2026',
    venue: 'SoFi Stadium · Inglewood',
    stage: 'ROUND_OF_16', tie: '1A × 3CDF', status: 'SCHEDULED',
    home: 'MEX', away: 'CRO',
  },
  {
    id: 14, day: 'Sáb 28/Jun', kickoff: '17:00', date: '28/06/2026',
    venue: 'MetLife · East Rutherford',
    stage: 'ROUND_OF_16', tie: '1C × 2D', status: 'SCHEDULED',
    home: 'BRA', away: 'BEL',
  },
  {
    id: 15, day: 'Dom 29/Jun', kickoff: '13:00', date: '29/06/2026',
    venue: 'AT&T · Arlington',
    stage: 'ROUND_OF_16', tie: '1E × 2F', status: 'SCHEDULED',
    home: 'ARG', away: 'GER',
  },
  {
    id: 16, day: 'Dom 29/Jun', kickoff: '17:00', date: '29/06/2026',
    venue: 'Mercedes-Benz · Atlanta',
    stage: 'ROUND_OF_16', tie: '1F × 2E', status: 'SCHEDULED',
    home: 'ESP', away: 'POR',
  },

  // ── MATA-MATA · QUARTAS — confronto ainda indefinido (TBD) ──
  {
    id: 17, day: 'Qui 03/Jul', kickoff: '16:00', date: '03/07/2026',
    venue: 'Hard Rock · Miami',
    stage: 'QUARTER_FINALS', tie: 'QF1', status: 'SCHEDULED',
    homeSlot: 'Venc. Oitavas 1', awaySlot: 'Venc. Oitavas 2',
  },
  {
    id: 18, day: 'Sex 04/Jul', kickoff: '16:00', date: '04/07/2026',
    venue: 'Lumen Field · Seattle',
    stage: 'QUARTER_FINALS', tie: 'QF2', status: 'SCHEDULED',
    homeSlot: 'Venc. Oitavas 3', awaySlot: 'Venc. Oitavas 4',
  },

  // ── MATA-MATA · SEMIFINAL — confronto ainda indefinido (TBD) ──
  {
    id: 19, day: 'Ter 08/Jul', kickoff: '16:00', date: '08/07/2026',
    venue: 'AT&T · Arlington',
    stage: 'SEMI_FINALS', tie: 'SF1', status: 'SCHEDULED',
    homeSlot: 'Venc. Quartas 1', awaySlot: 'Venc. Quartas 2',
  },

  // ── MATA-MATA · FINAL — confronto ainda indefinido (TBD) ──
  {
    id: 20, day: 'Dom 19/Jul', kickoff: '15:00', date: '19/07/2026',
    venue: 'MetLife · East Rutherford',
    stage: 'FINAL', tie: 'A GRANDE DECISÃO', status: 'SCHEDULED',
    homeSlot: 'Venc. Semi 1', awaySlot: 'Venc. Semi 2',
  },
];

export const POOLS: Pool[] = [
  {
    id: 'pool-resenha',
    name: 'Resenha do Bar',
    code: 'BARRES',
    members: 12,
    isPublic: false,
    isMember: true,
    myRank: 3,
    myPoints: 21,
    leader: 'Helena Vasques',
    leaderPoints: 28,
    lastResults: [3, 1, 3],
    accent: 'magenta',
  },
  {
    id: 'pool-trampo',
    name: 'Trampo FC',
    code: 'TRAMPO',
    members: 47,
    isPublic: false,
    isMember: true,
    myRank: 11,
    myPoints: 15,
    leader: 'Diretoria',
    leaderPoints: 32,
    lastResults: [1, 0, 1],
    accent: 'cobalt',
  },
  {
    id: 'pool-publico',
    name: 'Geral Brasil 🇧🇷',
    code: 'BRASIL',
    members: 8431,
    isPublic: true,
    isMember: true,
    myRank: 1281,
    myPoints: 18,
    leader: '@neto88',
    leaderPoints: 41,
    lastResults: [3, 0, 1],
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

// ── "EU" — perfil do jogador, agregado entre todos os bolões (não por bolão) ──
export const ME: PlayerProfile = {
  name: 'Você',
  handle: '@vc',
  avatar: 'VC',
  tone: 'lime',
  seasonPoints: 54,   // soma de pontos em todos os bolões
  hitRate: 68,        // % de palpites que pontuaram
  streak: 4,          // sequência atual de palpites que pontuaram
  bestRank: 3,        // melhor colocação entre todos os bolões
  bestPool: 'Resenha do Bar',
  totalGuesses: 22,
  exactCount: 7,      // placares cravados
};

// ── Grupos da Copa (tela de Jogos) — 12 chaves A..L com tabela e forma ──
export const ALL_GROUPS_STANDINGS: GroupFull[] = ([
  { g: 'A', rows: [
    { code: 'MEX', name: 'México',     iso: 'mx', P: 2, V: 2, E: 0, D: 0, GP: 5, GC: 1, pts: 6, form: ['W','W','-'] },
    { code: 'ECU', name: 'Equador',    iso: 'ec', P: 2, V: 1, E: 1, D: 0, GP: 3, GC: 2, pts: 4, form: ['W','D','-'] },
    { code: 'CAN', name: 'Canadá',     iso: 'ca', P: 2, V: 0, E: 1, D: 1, GP: 1, GC: 3, pts: 1, form: ['L','D','-'] },
    { code: 'SUI', name: 'Suíça',      iso: 'ch', P: 2, V: 0, E: 0, D: 2, GP: 1, GC: 4, pts: 0, form: ['L','L','-'] },
  ]},
  { g: 'B', rows: [
    { code: 'ENG', name: 'Inglaterra', iso: 'gb-eng', P: 2, V: 2, E: 0, D: 0, GP: 4, GC: 0, pts: 6, form: ['W','W','-'] },
    { code: 'NED', name: 'P. Baixos',  iso: 'nl', P: 2, V: 1, E: 0, D: 1, GP: 3, GC: 2, pts: 3, form: ['W','L','-'] },
    { code: 'USA', name: 'EUA',        iso: 'us', P: 2, V: 1, E: 0, D: 1, GP: 2, GC: 2, pts: 3, form: ['L','W','-'] },
    { code: 'IRN', name: 'Irã',        iso: 'ir', P: 2, V: 0, E: 0, D: 2, GP: 0, GC: 5, pts: 0, form: ['L','L','-'] },
  ]},
  { g: 'C', rows: [
    { code: 'BRA', name: 'Brasil',     iso: 'br', P: 2, V: 2, E: 0, D: 0, GP: 5, GC: 2, pts: 6, form: ['W','W','-'] },
    { code: 'CRO', name: 'Croácia',    iso: 'hr', P: 2, V: 1, E: 0, D: 1, GP: 3, GC: 3, pts: 3, form: ['W','L','-'] },
    { code: 'ECU', name: 'Equador',    iso: 'ec', P: 2, V: 1, E: 0, D: 1, GP: 2, GC: 2, pts: 3, form: ['L','W','-'] },
    { code: 'CMR', name: 'Camarões',   iso: 'cm', P: 2, V: 0, E: 0, D: 2, GP: 1, GC: 4, pts: 0, form: ['L','L','-'] },
  ]},
  { g: 'D', rows: [
    { code: 'FRA', name: 'França',     iso: 'fr', P: 2, V: 1, E: 1, D: 0, GP: 4, GC: 2, pts: 4, form: ['W','D','-'] },
    { code: 'BEL', name: 'Bélgica',    iso: 'be', P: 2, V: 1, E: 1, D: 0, GP: 3, GC: 2, pts: 4, form: ['D','W','-'] },
    { code: 'POL', name: 'Polônia',    iso: 'pl', P: 2, V: 0, E: 1, D: 1, GP: 2, GC: 3, pts: 1, form: ['L','D','-'] },
    { code: 'TUN', name: 'Tunísia',    iso: 'tn', P: 2, V: 0, E: 1, D: 1, GP: 1, GC: 3, pts: 1, form: ['D','L','-'] },
  ]},
  { g: 'E', rows: [
    { code: 'ARG', name: 'Argentina',  iso: 'ar', P: 2, V: 2, E: 0, D: 0, GP: 5, GC: 1, pts: 6, form: ['W','W','-'] },
    { code: 'POR', name: 'Portugal',   iso: 'pt', P: 2, V: 1, E: 1, D: 0, GP: 3, GC: 1, pts: 4, form: ['W','D','-'] },
    { code: 'URU', name: 'Uruguai',    iso: 'uy', P: 2, V: 0, E: 1, D: 1, GP: 1, GC: 2, pts: 1, form: ['L','D','-'] },
    { code: 'KOR', name: 'Coreia Sul', iso: 'kr', P: 2, V: 0, E: 0, D: 2, GP: 1, GC: 6, pts: 0, form: ['L','L','-'] },
  ]},
  { g: 'F', rows: [
    { code: 'ESP', name: 'Espanha',    iso: 'es', P: 2, V: 2, E: 0, D: 0, GP: 6, GC: 1, pts: 6, form: ['W','W','-'] },
    { code: 'GER', name: 'Alemanha',   iso: 'de', P: 2, V: 1, E: 0, D: 1, GP: 4, GC: 2, pts: 3, form: ['L','W','-'] },
    { code: 'JPN', name: 'Japão',      iso: 'jp', P: 2, V: 1, E: 0, D: 1, GP: 2, GC: 2, pts: 3, form: ['W','L','-'] },
    { code: 'MAR', name: 'Marrocos',   iso: 'ma', P: 2, V: 0, E: 0, D: 2, GP: 0, GC: 7, pts: 0, form: ['L','L','-'] },
  ]},
  { g: 'G', rows: [
    { code: 'ITA', name: 'Itália',     iso: 'it', P: 2, V: 1, E: 1, D: 0, GP: 3, GC: 1, pts: 4, form: ['D','W','-'] },
    { code: 'COL', name: 'Colômbia',   iso: 'co', P: 2, V: 1, E: 1, D: 0, GP: 4, GC: 2, pts: 4, form: ['W','D','-'] },
    { code: 'SEN', name: 'Senegal',    iso: 'sn', P: 2, V: 1, E: 0, D: 1, GP: 2, GC: 3, pts: 3, form: ['W','L','-'] },
    { code: 'NZL', name: 'N. Zelândia',iso: 'nz', P: 2, V: 0, E: 0, D: 2, GP: 1, GC: 4, pts: 0, form: ['L','L','-'] },
  ]},
  { g: 'H', rows: [
    { code: 'NED', name: 'P. Baixos',  iso: 'nl', P: 2, V: 2, E: 0, D: 0, GP: 4, GC: 1, pts: 6, form: ['W','W','-'] },
    { code: 'AUT', name: 'Áustria',    iso: 'at', P: 2, V: 1, E: 0, D: 1, GP: 3, GC: 2, pts: 3, form: ['W','L','-'] },
    { code: 'CRC', name: 'C. Rica',    iso: 'cr', P: 2, V: 0, E: 1, D: 1, GP: 1, GC: 2, pts: 1, form: ['L','D','-'] },
    { code: 'KSA', name: 'A. Saudita', iso: 'sa', P: 2, V: 0, E: 1, D: 1, GP: 1, GC: 4, pts: 1, form: ['D','L','-'] },
  ]},
  { g: 'I', rows: [
    { code: 'POR', name: 'Portugal',   iso: 'pt', P: 2, V: 2, E: 0, D: 0, GP: 5, GC: 0, pts: 6, form: ['W','W','-'] },
    { code: 'SCO', name: 'Escócia',    iso: 'gb-sct', P: 2, V: 1, E: 0, D: 1, GP: 2, GC: 2, pts: 3, form: ['L','W','-'] },
    { code: 'AUS', name: 'Austrália',  iso: 'au', P: 2, V: 0, E: 1, D: 1, GP: 1, GC: 2, pts: 1, form: ['D','L','-'] },
    { code: 'PAR', name: 'Paraguai',   iso: 'py', P: 2, V: 0, E: 1, D: 1, GP: 0, GC: 4, pts: 1, form: ['L','D','-'] },
  ]},
  { g: 'J', rows: [
    { code: 'BEL', name: 'Bélgica',    iso: 'be', P: 2, V: 1, E: 1, D: 0, GP: 3, GC: 1, pts: 4, form: ['W','D','-'] },
    { code: 'CIV', name: 'Costa Marf.',iso: 'ci', P: 2, V: 1, E: 1, D: 0, GP: 2, GC: 1, pts: 4, form: ['D','W','-'] },
    { code: 'QAT', name: 'Catar',      iso: 'qa', P: 2, V: 0, E: 1, D: 1, GP: 1, GC: 2, pts: 1, form: ['L','D','-'] },
    { code: 'NZL', name: 'N. Zelândia',iso: 'nz', P: 2, V: 0, E: 1, D: 1, GP: 0, GC: 2, pts: 1, form: ['D','L','-'] },
  ]},
  { g: 'K', rows: [
    { code: 'DEN', name: 'Dinamarca',  iso: 'dk', P: 2, V: 2, E: 0, D: 0, GP: 4, GC: 1, pts: 6, form: ['W','W','-'] },
    { code: 'SRB', name: 'Sérvia',     iso: 'rs', P: 2, V: 1, E: 0, D: 1, GP: 3, GC: 2, pts: 3, form: ['L','W','-'] },
    { code: 'CHI', name: 'Chile',      iso: 'cl', P: 2, V: 1, E: 0, D: 1, GP: 2, GC: 3, pts: 3, form: ['W','L','-'] },
    { code: 'GHA', name: 'Gana',       iso: 'gh', P: 2, V: 0, E: 0, D: 2, GP: 1, GC: 4, pts: 0, form: ['L','L','-'] },
  ]},
  { g: 'L', rows: [
    { code: 'GER', name: 'Alemanha',   iso: 'de', P: 2, V: 2, E: 0, D: 0, GP: 5, GC: 2, pts: 6, form: ['W','W','-'] },
    { code: 'NOR', name: 'Noruega',    iso: 'no', P: 2, V: 1, E: 1, D: 0, GP: 4, GC: 2, pts: 4, form: ['W','D','-'] },
    { code: 'PAN', name: 'Panamá',     iso: 'pa', P: 2, V: 0, E: 1, D: 1, GP: 1, GC: 3, pts: 1, form: ['D','L','-'] },
    { code: 'EGY', name: 'Egito',      iso: 'eg', P: 2, V: 0, E: 0, D: 2, GP: 1, GC: 4, pts: 0, form: ['L','L','-'] },
  ]},
] as Omit<GroupFull, 'id'>[]).map((grp, i) => ({ id: i + 1, ...grp }));

// Fases do mata-mata (tela de Jogos) — ordem da segunda fase até a final.
export const KNOCKOUT_STAGES: KnockoutStage[] = [
  { id: 'SECOND_ROUND',   label: 'Segunda fase',     short: '2ª FASE', accent: 'cobalt',  note: '32 SELEÇÕES · PRIMEIRO CORTE' },
  { id: 'ROUND_OF_16',    label: 'Oitavas de final', short: 'OITAVAS', accent: 'magenta', note: '16 SELEÇÕES · ELIMINATÓRIO' },
  { id: 'QUARTER_FINALS', label: 'Quartas de final', short: 'QUARTAS', accent: 'coral',   note: '8 SELEÇÕES · MORTE SÚBITA' },
  { id: 'SEMI_FINALS',    label: 'Semifinal',        short: 'SEMI',    accent: 'cobalt',  note: '4 SELEÇÕES · VALENDO A VAGA' },
  { id: 'FINAL',          label: 'Final',            short: 'FINAL',   accent: 'lime',    note: '2 SELEÇÕES · A TAÇA' },
];

// Monta os 6 jogos de um grupo, reaproveitando partidas reais já cadastradas e
// completando com fixtures placeholder (round-robin) pra preencher a chave.
export function getGroupMatches(g: string): Match[] {
  const real = MATCHES.filter(m => m.stage === 'GROUP_STAGE' && m.group === g);
  const stand = ALL_GROUPS_STANDINGS.find(x => x.g === g);
  if (!stand) return real;
  const t = stand.rows.map(r => r.code);
  const pairs = [[0, 1], [2, 3], [0, 2], [1, 3], [0, 3], [1, 2]];
  const venues = ['SoFi Stadium', 'MetLife', 'AT&T Stadium', 'Hard Rock', 'Lumen Field', 'BMO Field'];
  // Synthetic fixtures get a stable, non-colliding numeric id (real ids are ≤ 24).
  const base = 9000 + (g.charCodeAt(0) - 65) * 10;
  return pairs.map(([i, j], k) => {
    const found = real.find(m =>
      (m.home === t[i] && m.away === t[j]) || (m.home === t[j] && m.away === t[i]));
    if (found) return found;
    return {
      id: base + k, group: g, stage: 'GROUP_STAGE', status: 'SCHEDULED',
      home: t[i], away: t[j],
      day: 'A definir', kickoff: '--:--',
      venue: venues[k % venues.length] + ' · Copa 26',
    } as Match;
  });
}

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

function ago(ms: number): string {
  return new Date(Date.now() - ms).toISOString();
}

// Pools do seeder: id 1 = Bolão da Família, id 2 = Bolão Público, id 3 = Bolão do Trampo
// Usuários do seeder: alice (isMe), User01–User10, bob, carol
// Partidas finalizadas: BRA 2×1 MEX, ARG 1×1 FRA, ESP 3×0 POR, GHA 0×2 BEL, JPN 1×2 COL
export const ACTIVITY_FEED: ActivityItem[] = [
  {
    id: 1, poolId: 1, poolName: 'Bolão da Família',
    createdAt: ago(2 * 60_000),
    actor: 'User01', isMe: false,
    action: 'cravou', subject: 'BRA 2×1 MEX', points: 3,
  },
  {
    id: 2, poolId: 1, poolName: 'Bolão da Família',
    createdAt: ago(4 * 60_000),
    actor: 'Alice', isMe: true,
    action: 'acertou', subject: 'vencedor', points: 1,
  },
  {
    id: 3, poolId: 3, poolName: 'Bolão do Trampo',
    createdAt: ago(9 * 60_000),
    actor: 'Carol', isMe: false,
    action: 'cravou', subject: 'ESP 3×0 POR', points: 3,
  },
  {
    id: 4, poolId: 2, poolName: 'Bolão Público',
    createdAt: ago(22 * 60_000),
    actor: 'Bob', isMe: false,
    action: 'errou', subject: 'ARG 0×0 FRA', points: 0,
  },
  {
    id: 5, poolId: 1, poolName: 'Bolão da Família',
    createdAt: ago(60 * 60_000),
    actor: 'User03', isMe: false,
    action: 'errou', subject: 'JPN 2×3 COL', points: 0,
  },
  {
    id: 6, poolId: 3, poolName: 'Bolão do Trampo',
    createdAt: ago(65 * 60_000),
    actor: 'Alice', isMe: true,
    action: 'palpitou', subject: 'GER 1×1 ESP', points: null,
  },
  {
    id: 7, poolId: 2, poolName: 'Bolão Público',
    createdAt: ago(2 * 3_600_000),
    actor: 'User02', isMe: false,
    action: 'acertou', subject: 'vencedor', points: 1,
  },
  {
    id: 8, poolId: 1, poolName: 'Bolão da Família',
    createdAt: ago(3 * 3_600_000),
    actor: 'Bob', isMe: false,
    action: 'cravou', subject: 'URU 2×0 CAN', points: 3,
  },
  {
    id: 9, poolId: 3, poolName: 'Bolão do Trampo',
    createdAt: ago(4 * 3_600_000),
    actor: 'User04', isMe: false,
    action: 'errou', subject: 'ESP 2×1 POR', points: 0,
  },
  {
    id: 10, poolId: 2, poolName: 'Bolão Público',
    createdAt: ago(5 * 3_600_000),
    actor: 'User05', isMe: false,
    action: 'acertou', subject: 'vencedor', points: 1,
  },
  {
    id: 11, poolId: 1, poolName: 'Bolão da Família',
    createdAt: ago(6 * 3_600_000),
    actor: 'Alice', isMe: true,
    action: 'cravou', subject: 'ESP 3×0 POR', points: 3,
  },
  {
    id: 12, poolId: 3, poolName: 'Bolão do Trampo',
    createdAt: ago(8 * 3_600_000),
    actor: 'User07', isMe: false,
    action: 'palpitou', subject: 'USA 1×1 CAN', points: null,
  },
  {
    id: 13, poolId: 2, poolName: 'Bolão Público',
    createdAt: ago(9 * 3_600_000),
    actor: 'User08', isMe: false,
    action: 'cravou', subject: 'GHA 0×2 BEL', points: 3,
  },
  {
    id: 14, poolId: 1, poolName: 'Bolão da Família',
    createdAt: ago(12 * 3_600_000),
    actor: 'Alice', isMe: true,
    action: 'acertou', subject: 'vencedor', points: 1,
  },
];

const PUB_ACCENTS: Tone[] = ['magenta', 'cobalt', 'lime', 'coral'];

export interface PublicPool {
  id: string;
  name: string;
  code: string;
  members: number;
  tag: string;
  leader: string;
  you: number | null;
  accent: Tone;
}

export const PUBLIC_POOLS: PublicPool[] = (
  [
    { name: 'Geral Brasil',           code: 'BRASIL', members: 8431, tag: 'POPULAR', leader: '@neto88',    you: 1281 },
    { name: 'Hexa ou Choro',          code: 'HEXACL', members: 2103, tag: 'NOVO',    leader: '@duda_rj',   you: null },
    { name: 'Bar do Geninho',         code: 'BARGEN', members: 412,  tag: 'LOCAL',   leader: 'Geninho',    you: null },
    { name: 'Engenheiros da Bola',    code: 'ENGBOL', members: 184,  tag: '',        leader: 'Diretoria',  you: null },
    { name: 'Mães de Copa',           code: 'MAESCO', members: 698,  tag: 'NOVO',    leader: '@dona_ana',  you: null },
    { name: 'Universitário 26',       code: 'UNI026', members: 1244, tag: '',        leader: '@grenal12',  you: null },
    { name: 'Quartas de Final',       code: 'QFINAL', members: 542,  tag: '',        leader: '@tato',      you: 88 },
    { name: 'Galera do Trampo',       code: 'GALTRA', members: 327,  tag: '',        leader: '@rh_ana',    you: null },
    { name: 'Sudeste Unido',          code: 'SUDest', members: 3120, tag: 'POPULAR', leader: '@mineiro',   you: null },
    { name: 'Norte Forte',            code: 'NORfor', members: 891,  tag: '',        leader: '@belem22',   you: null },
    { name: 'Resenha Nordestina',     code: 'NORDES', members: 2740, tag: 'POPULAR', leader: '@recife',    you: null },
    { name: 'Pampa FC',               code: 'PAMPA0', members: 654,  tag: 'LOCAL',   leader: '@gaucho',    you: null },
    { name: 'Office League BH',       code: 'OFFBH0', members: 211,  tag: '',        leader: '@bhz',       you: null },
    { name: 'Tias do WhatsApp',       code: 'TIASWA', members: 1502, tag: 'NOVO',    leader: '@tia_cida',  you: null },
    { name: 'Faculdade de Direito',   code: 'DIREITO',members: 438,  tag: '',        leader: '@oab2026',   you: null },
    { name: 'Várzea Premier',         code: 'VARZEA', members: 967,  tag: 'LOCAL',   leader: '@pelada',    you: null },
    { name: 'Copa do Condomínio',     code: 'CONDOM', members: 156,  tag: '',        leader: '@sindico',   you: null },
    { name: 'Sala dos Professores',   code: 'PROFES', members: 289,  tag: '',        leader: '@profe_ed',  you: null },
    { name: 'Torcida Mista',          code: 'MISTA0', members: 4120, tag: 'POPULAR', leader: '@neutro',    you: null },
    { name: 'Amigos da Pelada',       code: 'PELADA', members: 743,  tag: '',        leader: '@zaga10',    you: null },
    { name: 'Crônicas da Copa',       code: 'CRONIC', members: 1890, tag: 'NOVO',    leader: '@jornal',    you: null },
    { name: 'República dos Calouros', code: 'REPCAL', members: 376,  tag: 'LOCAL',   leader: '@vetera',    you: null },
    { name: 'Sócios do Bar 442',      code: 'BAR442', members: 512,  tag: '',        leader: '@chopp',     you: null },
    { name: 'Família Mundialista',    code: 'FAMMUN', members: 1067, tag: '',        leader: '@vovô_zé',   you: null },
    { name: 'Plantão da Madrugada',   code: 'PLANTA', members: 198,  tag: 'NOVO',    leader: '@coruja',    you: null },
    { name: 'Liga dos Apostadores',   code: 'LIGAAP', members: 6233, tag: 'POPULAR', leader: '@palpite1',  you: null },
  ] as Omit<PublicPool, 'id' | 'accent'>[]
).map((p, i) => ({ id: 'pub-' + i, accent: PUB_ACCENTS[i % PUB_ACCENTS.length], ...p }));

export function toneVar(tone: string | undefined): string {
  return TONE_TO_VAR[tone || 'ink'] || 'var(--ink)';
}

export function toneFg(tone: string | undefined): string {
  return tone === 'lime' || tone === 'paper' ? 'var(--ink)' : 'var(--paper)';
}
