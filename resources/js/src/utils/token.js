const TOKEN_KEY = 'sct'; // seller center token
const TOKEN_EXPIRED_DATE_KEY = 'token_expired_date';
const REFRESH_TOKEN_KEY = 'refresh_token';
const USER_KEY = 'user';

const Token = {
  getToken() {
    return localStorage.getItem(TOKEN_KEY) || sessionStorage.getItem(TOKEN_KEY);
  },

  hasToken() {
    return this.getToken() !== null;
  },

  setToken(accessToken, isRemember) {
    sessionStorage.setItem(TOKEN_KEY, accessToken);
    localStorage.setItem(TOKEN_KEY, accessToken);
  },

  removeToken() {
    sessionStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(TOKEN_KEY);
  },

  getUser() {
    return JSON.parse(localStorage.getItem(USER_KEY));
  },

  setUser(user) {
    localStorage.setItem(USER_KEY, JSON.stringify(user));
  },

  removeUser() {
    localStorage.removeItem(USER_KEY);
  },

  getTokenExpiredDate() {
    return localStorage.getItem(TOKEN_EXPIRED_DATE_KEY);
  },

  setTokenExpiredDate(expiredDate) {
    localStorage.setItem(TOKEN_EXPIRED_DATE_KEY, expiredDate);
  },

  removeTokenExpiredDate() {
    localStorage.removeItem(TOKEN_EXPIRED_DATE_KEY);
  },

  getRefreshToken() {
    return localStorage.getItem(REFRESH_TOKEN_KEY);
  },

  setRefreshToken(refreshToken) {
    localStorage.setItem(REFRESH_TOKEN_KEY, refreshToken);
  },

  removeRefreshToken() {
    localStorage.removeItem(REFRESH_TOKEN_KEY);
  },
};

export default Token;
