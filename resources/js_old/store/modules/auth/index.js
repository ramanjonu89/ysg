import TokenService from '@/services/Token';
import request from '@/utils/requests';

const state = {
    token: TokenService.getToken() || '',
    status: '',
    errorMessage: '',
    user : {}
}

const getters = {
    isLoggedIn: state => !!state.token,
    authStatus: state => state.status,
    errorMessage: state => state.errorMessage,
}

const actions = {
    async login({commit}, [email, password]) {
        const response = await request({
            url: 'api/v1/auth/login',
            method: 'POST',
            data: {
                email: email,
                password: password,
            }
        });

        if (response.success) {
            const user = response.data.user
            const token = response.data.token

            TokenService.setToken(token);
            TokenService.setUser(user);

            // change auth state
            commit('auth_success', token, user)
            return true;
        } else {
            commit('auth_error', response.message)
            TokenService.removeToken();
            TokenService.removeUser();
            return false;
        }
    },
    logout({commit}) {
        return new Promise((resolve, reject) => {
            commit('logout')
            TokenService.removeToken();
            TokenService.removeUser();
            resolve()
        })
    }
}

const mutations = {
    auth_request(state) {
        state.status = 'loading'
    },
    auth_success(state, token, user) {
        state.status = 'success'
        state.token = token
        state.user = user
    },
    auth_error(state, errorMessage) {
        state.status = 'error';
        state.errorMessage = errorMessage;
    },
    logout(state) {
        state.status = ''
        state.token = ''
    }
}

export default {
    state,
    mutations,
    getters,
    actions
}
