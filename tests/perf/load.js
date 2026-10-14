import http from 'k6/http';
import { check, sleep } from 'k6';

const mode = __ENV.MODE || 'nominal';
const profiles = {
  nominal: [{ duration: '30s', target: 100 }, { duration: '10m', target: 100 }, { duration: '30s', target: 0 }],
  stress: [{ duration: '2m', target: 100 }, { duration: '2m', target: 300 }, { duration: '2m', target: 600 }, { duration: '2m', target: 1000 }, { duration: '2m', target: 0 }],
  spike: [{ duration: '1m', target: 50 }, { duration: '1m', target: 1000 }, { duration: '1m', target: 1000 }, { duration: '2m', target: 50 }, { duration: '1m', target: 0 }],
  soak: [{ duration: '1m', target: 50 }, { duration: '4h', target: 50 }, { duration: '1m', target: 0 }]
};
export const options = { stages: profiles[mode], thresholds: { http_req_duration: ['p(95)<2000'], http_req_failed: ['rate<0.01'], checks: ['rate>0.99'] } };
const base = __ENV.BASE_URL || 'http://localhost/blog-noticias/public';
export default function () {
  const home = http.get(base + '/', { tags: { caso: 'EF-20 EF-21 EF-22 EF-23 EF-24' } });
  check(home, { 'EF-21 EF-23: portada responde sin fallos': response => response.status === 200 });
  const search = http.get(base + '/buscar?q=comunidad', { tags: { caso: 'EF-25 EF-28' } });
  check(search, { 'EF-25 EF-28: búsqueda responde': response => response.status === 200 });
  sleep(1);
}
