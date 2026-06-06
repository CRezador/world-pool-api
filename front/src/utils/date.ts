export function parseApiDate(kickoff: string): Date {
  const [day, month, yearAndTime] = kickoff.split('/');
  const [year, time] = yearAndTime.split(' ');
  const [hour = '0', minute = '0'] = time ? time.split(':') : [];
  return new Date(+year, +month - 1, +day, +hour, +minute);
}

export function formatKickoff(kickoff: string | null | undefined): string {
  if (!kickoff) return 'A DEFINIR';
  const d = parseApiDate(kickoff);
  const day = d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
  const time = d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
  return `${day} · ${time}`;
}

export function formatKickoffShort(kickoff: string | null | undefined): string {
  if (!kickoff) return 'A DEFINIR';
  const d = parseApiDate(kickoff);
  const day = d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
  const time = d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
  return `${day} · ${time}`;
}
