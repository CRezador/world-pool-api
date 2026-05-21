export interface StandingTeamRow {
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
  form: Array<'W' | 'D' | 'L' | '-'>;
}

export interface StandingGroup {
  g: string;
  rows: StandingTeamRow[];
}

export const STANDINGS_ALL: StandingGroup[] = [
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
];
