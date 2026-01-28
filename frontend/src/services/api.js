import axios from 'axios';

// Use environment variable if available, otherwise use default
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:3000/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json'
  }
});

// Championships
export const championshipsAPI = {
  getAll: () => api.get('/championships'),
  getById: (id) => api.get(`/championships/${id}`),
  create: (data) => api.post('/championships', data),
  update: (id, data) => api.put(`/championships/${id}`, data),
  delete: (id) => api.delete(`/championships/${id}`),
  getStandings: (id) => api.get(`/championships/standings/${id}`),
  getTopScorers: (id) => api.get(`/championships/scorers/${id}`),
  getTopAssisters: (id) => api.get(`/championships/assisters/${id}`),
  getCardsStats: (id) => api.get(`/championships/cards/${id}`)
};

// Teams
export const teamsAPI = {
  getAll: () => api.get('/teams'),
  getById: (id) => api.get(`/teams/${id}`),
  create: (data) => api.post('/teams', data),
  update: (id, data) => api.put(`/teams/${id}`, data),
  delete: (id) => api.delete(`/teams/${id}`),
  getChampionships: (id) => api.get(`/teams/stats/${id}`),
  getTitles: (id) => api.get(`/teams/titles/${id}`),
  getHeadToHead: (team1Id, team2Id) => api.get(`/teams/head-to-head/${team1Id}/${team2Id}`),
  getTopScorers: (id) => api.get(`/teams/top-scorers/${id}`)
};

// Players
export const playersAPI = {
  getAll: () => api.get('/players'),
  getById: (id) => api.get(`/players/${id}`),
  create: (data) => api.post('/players', data),
  update: (id, data) => api.put(`/players/${id}`, data),
  delete: (id) => api.delete(`/players/${id}`),
  getStats: (id) => api.get(`/players/stats/${id}`),
  getMatches: (id, limit = null) => api.get(`/players/matches/${id}${limit ? `?limit=${limit}` : ''}`),
  getGoalsByRival: (id) => api.get(`/players/goals-by-rival/${id}`),
  getStatsByChampionship: (id) => api.get(`/players/by-championship/${id}`)
};

// Matches
export const matchesAPI = {
  getAll: (championshipId = null) => api.get(`/matches${championshipId ? `?championship_id=${championshipId}` : ''}`),
  getById: (id) => api.get(`/matches/${id}`),
  create: (data) => api.post('/matches', data),
  update: (id, data) => api.put(`/matches/${id}`, data),
  delete: (id) => api.delete(`/matches/${id}`),
  getLineup: (id) => api.get(`/matches/lineup/${id}`),
  getGoals: (id) => api.get(`/matches/goals/${id}`),
  getCards: (id) => api.get(`/matches/cards/${id}`),
  addGoal: (data) => api.post('/matches/goal', data),
  addCard: (data) => api.post('/matches/card', data),
  addLineup: (data) => api.post('/matches/lineup', data)
};

// Coaches
export const coachesAPI = {
  getAll: () => api.get('/coaches'),
  getById: (id) => api.get(`/coaches/${id}`),
  create: (data) => api.post('/coaches', data),
  update: (id, data) => api.put(`/coaches/${id}`, data),
  delete: (id) => api.delete(`/coaches/${id}`),
  getStats: (id) => api.get(`/coaches/stats/${id}`)
};

// Referees
export const refereesAPI = {
  getAll: () => api.get('/referees'),
  getById: (id) => api.get(`/referees/${id}`),
  create: (data) => api.post('/referees', data),
  update: (id, data) => api.put(`/referees/${id}`, data),
  delete: (id) => api.delete(`/referees/${id}`),
  getStats: (id) => api.get(`/referees/stats/${id}`),
  getMatches: (id) => api.get(`/referees/matches/${id}`)
};

// Export endpoints
export const exportAPI = {
  exportPDF: (type, id) => {
    window.open(`${API_BASE_URL}/export/pdf/${type}/${id}`, '_blank');
  },
  exportExcel: (type, id) => {
    window.open(`${API_BASE_URL}/export/excel/${type}/${id}`, '_blank');
  }
};

export default api;
