import http from 'k6/http';
import { check, sleep } from 'k6';
import { SharedArray } from 'k6/data';

// Список URL для тестирования
const urls = new SharedArray('urls', function () {
    return [
        '/v2/meetings/previous',
        '/v2/club',
        '/courses-v2',
    ];
});

export const options = {
  // Имитируем поведение 20 одновременных пользователей
  vus: 5,
  // Тест будет длиться 1 минуту
  duration: '30s',
  thresholds: {
    // Условие: 95% запросов должны завершаться менее чем за 800ms
    http_req_duration: ['p(95)<800'],
    // Условие: менее 1% запросов должны завершаться с ошибкой
    http_req_failed: ['rate<0.01'],
  },
};

export default function () {
  const baseUrl = `http://217.198.12.212`;
  // Выбираем случайный URL из списка
  const url = urls[Math.floor(Math.random() * urls.length)];
  
  const res = http.get(`${baseUrl}${url}`);

  check(res, {
    'status was 200': (r) => r.status == 200,
    'transaction time OK': (r) => r.timings.duration < 1000,
  });

  // Пауза от 1 до 3 секунд, чтобы имитировать поведение реального пользователя
  sleep(Math.random() * 2 + 1);
} 