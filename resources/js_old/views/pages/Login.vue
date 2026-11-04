<template>
  <div class="login-page">
    <CWrapper>
      <CHeader fixed with-subheader light class="position-fixed">
        <nav class="navbar">
          <div class="container">
            <a class="navbar-brand" href="/">
              <img src="frontend/images/logo.png" alt="YSG Seller Center" height="45">
              <span class="brand-text">SELLER CENTER</span>
            </a>
          </div>
        </nav>
      </CHeader>

      <main class="c-app flex-row align-items-center">
        <CContainer>
          <CRow class="justify-content-center">
            <CCol md="6">
              <CCardGroup>
                <CCard class="p-4">
                  <CCardBody>
                    <CForm class="text-center" v-on:submit.prevent="handleSubmit">
                      <h1>{{ $t('Seller login') }}</h1>
                      <p class="text-muted">{{ $t('Sign In to your account') }}</p>
                      <CAlert color="danger" :show="hasError">
                        {{ errorMessage }}
                      </CAlert>
                      <div class="form-group">
                        <input v-validate="'required|email'" v-model="loginForm.email" type="text" class="form-control" name="email" id="email" :placeholder="$t('Email')" autocomplete="email" />
                        <span class="invalid-feedback">{{ errors.first('email') }}</span>
                      </div>
                      <div class="form-group">
                        <input type="password" v-model="loginForm.password" class="form-control" id="passowrd" :placeholder="$t('Password')" autocomplete="password" />
                      </div>
                      <CRow>
                        <CCol col="12" class="text-center">
                          <CButton color="primary" class="px-4 btn-block btn-do-login" @click="handleSubmit">{{ $t('Login') }}</CButton>
                        </CCol>
                      </CRow>
                    </CForm>
                  </CCardBody>
                </CCard>
              </CCardGroup>
            </CCol>
          </CRow>
        </CContainer>
      </main>
    </CWrapper>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import TokenService from '@/services/Token';
export default {
  name: 'Login',
  data() {
    return {
      loginForm: {
        email: '',
        password: '',
      },
    };
  },
  computed: {
    ...mapGetters(['authStatus']),
    hasError() {
      return this.$store.state.auth.status === 'error';
    },
    errorMessage() {
      return this.$store.state.auth.errorMessage;
    }
  },
  methods: {
    ...mapActions([
      'login'
    ]),
    async handleSubmit(){
      if (this.loginForm.email.length > 0 && this.loginForm.password.length > 0) {
        const response = await this.login([this.loginForm.email, this.loginForm.password]);

        if (response === true) {
          const redirect = this.$route.query.redirect;
          if (redirect) {
            this.$router.push({ path: redirect});
          }
          this.$router.push({ name: 'Dashboard' });
        }
      }
    }
  },
}
</script>
