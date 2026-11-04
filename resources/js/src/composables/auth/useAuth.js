import request from '@/utils/requests';
import Token from "@/utils/token";
import { computed, reactive } from '@vue/composition-api';

const state = reactive({
  authenticating: false,
  error: undefined,
  token: Token.getToken() || '',
  isLoggedIn: Token.hasToken(),
  user: Token.getUser() || undefined,
})

export const useAuth = () => {
  // getters
  const user = computed(() => state.user)
  const authenticating = computed(() => state.authenticating)
  const error = computed(() => state.error)

  const specialChars = /[ !\-\+\*&@./"':\\,™®©?´`°$€%^~()]/g;
  const sellerCode = computed(() => {
    return state.user.name
      .replace(specialChars, '')
      .substring(0, 3)
      .toUpperCase();
  });

  // actions
  const login = async ([email, password, remember]) => {
    state.authenticating = true
    const response = await request({
      url: '/auth/login',
      method: 'POST',
      data: {
        email: email,
        password: password,
      }
    });

    let result = false

    if (response.error) {
      Token.removeToken();
      Token.removeUser();

      state.error = response.message
      state.user = undefined
    } else {
      const user = response.data.user
      const token = response.data.token

      Token.setToken(token, remember)
      Token.setUser(user)

      state.user = user
      result = true
    }

    state.authenticating = false
    console.log('state', state)
    console.log('user', user)
    return result
  }

  const logout = () => {
    Token.removeToken();
    Token.removeUser();

    state.user = undefined
    return true;
  }

  return {
    authenticating,
    error,
    user,
    sellerCode,
    login,
    logout
  }
}
