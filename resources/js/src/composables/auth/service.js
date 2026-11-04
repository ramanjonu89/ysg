import defaultConfig from "./defaultConfig";

export default class AuthService {
  axiosIns = null

  config = { ...defaultConfig }

  constructor(axiosIns, overrideConfig) {
    this.axiosIns = axiosIns
    this.config = { ...this.config, ...overrideConfig }
  }

  getToken() {
    return localStorage.getItem(this.config.storageTokenKeyName) || sessionStorage.getItem(this.config.storageTokenKeyName)
  }

  setToken(value, isRemember = false) {
    sessionStorage.setItem(this.config.storageTokenKeyName, value)
    if (isRemember) {
      localStorage.setItem(this.config.storageTokenKeyName, value)
    }
  }

  setUserData(value) {
    localStorage.setItem(this.config.storageUserData, value)
  }
}
