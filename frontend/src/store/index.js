import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

import router from '../router/'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    authToken: null,
    user: {
      name: null,
      email: null
    },
    error: {}
  },
  getters: {
    user (state) {
      return state.user
    },
    isAuthenticated (state) {
      return state.authToken !== null
    }
  },
  mutations: {
    authUser (state, userData) {
      state.authToken = userData.token
    },
    storeUser (state, userData) {
      state.user = userData
    },
    clearAuthData (state) {
      localStorage.removeItem('token')
      localStorage.removeItem('tokenExpiration')
      state.authToken = null
      state.user.name = null
      state.user.email = null
    },
    storeErrors (state, errors) {
      state.error = errors
    }
  },
  actions: {
    setLogoutTimer ({ dispatch }, expirationTime) {
      setTimeout(function () {
        dispatch('logout')
        console.log('Logout timer is over!')
      }, expirationTime * 1000)
    },
    login ({ commit, dispatch }, authData) {
      axios.post('/login', {
        email: authData.email,
        password: authData.password
      })
        .then(res => {
          if (res.data.is_error) {
            commit('storeErrors', res.data)
          } else {
            localStorage.setItem('token', res.data.access_token)
            const now = new Date()
            const expirationTime = new Date(now.getTime() + res.data.expires_in * 1000)
            localStorage.setItem('tokenExpiration', expirationTime)
            commit('authUser', {
              token: res.data.access_token
            })
            dispatch('fetchUser')
            dispatch('setLogoutTimer', res.data.expires_in)
            router.replace('/dashboard')
          }
        })
        .catch(res => {
          alert('Server-side Error occurred. Try again later.')
          commit('storeErrors', res)
        })
    },
    register ({ commit, dispatch }, data) {
      axios.post('/register', {
        email: data.email,
        name: data.name,
        password: data.password,
        password_confirmation: data.password_confirmation
      })
        .then(res => {
          if (res.data.is_error) {
            commit('storeErrors', res.data)
          } else {
            localStorage.setItem('token', res.data.access_token)
            const now = new Date()
            const expirationTime = new Date(now.getTime() + res.data.expires_in * 1000)
            localStorage.setItem('tokenExpiration', expirationTime)
            commit('authUser', {
              token: res.data.access_token
            })
            dispatch('fetchUser')
            dispatch('setLogoutTimer', res.data.expires_in)
            router.replace('/dashboard')
          }
        })
    },
    fetchUser ({ commit, state }) {
      axios.get(`/user?token=${state.authToken}`)
        .then(res => {
          commit('storeUser', res.data.data)
        })
        .catch(res => {
          commit('clearAuthData')
          router.replace('/login')
        })
    },
    logout ({ commit }) {
      commit('clearAuthData')
      router.replace('/login')
    },
    tryAutoLogin ({ commit, dispatch }) {
      const token = localStorage.getItem('token')
      const tokenExpiration = localStorage.getItem('tokenExpiration')
      const now = new Date()
      if (!token || now >= tokenExpiration) {
        commit('clearAuthData')
      } else {
        commit('authUser', {
          token: token
        })
        dispatch('fetchUser')
        router.replace('/dashboard')
      }
    }
  },
  modules: {
  }
})
