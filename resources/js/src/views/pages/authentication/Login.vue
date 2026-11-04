<template>
  <div class="auth-wrapper auth-v2">
    <b-row class="auth-inner m-0">

      <!-- Brand logo-->
      <b-link class="brand-logo">
        <b-img
          :src="appLogoImage"
          alt="logo"
          style="height: 40px"
        />
        <h2 class="brand-text text-primary ml-1">
          Seller Center
        </h2>
      </b-link>
      <!-- /Brand logo-->

      <!-- Left Text-->
      <b-col
        lg="8"
        class="d-none d-lg-flex align-items-center p-5"
      >
        <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
          <b-img
            fluid
            :src="sideImg"
            alt="Login V2"
          />
        </div>
      </b-col>
      <!-- /Left Text-->

      <!-- Login-->
      <b-col
        lg="4"
        class="d-flex align-items-center auth-bg px-2 p-lg-5"
      >
        <b-col
          sm="8"
          md="6"
          lg="12"
          class="px-xl-2 mx-auto"
        >
          <b-card-title
            title-tag="h2"
            class="font-weight-bold mb-1"
          >
            {{ $t('login.welcome') }}
          </b-card-title>
          <b-card-text class="mb-2">
            {{ $t('login.welcome_description') }}
          </b-card-text>

          <!-- form -->
          <validation-observer ref="loginValidation">
            <b-form
              class="auth-login-form mt-2"
              @submit.prevent
            >
              <!-- email -->
              <b-form-group
                :label="$t('form.input.email')"
                label-for="login-email"
              >
                <validation-provider
                  #default="{ errors }"
                  :name="$t('form.input.email')"
                  rules="required|email"
                >
                  <b-form-input
                    id="login-email"
                    v-model="emailInput"
                    :state="errors.length > 0 ? false:null"
                    name="login-email"
                    :placeholder="$t('form.placeholder.email')"
                    autofocus
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <!-- forgot password -->
              <b-form-group>
                <div class="d-flex justify-content-between">
                  <label for="login-password">{{ $t('form.input.password') }}</label>
                  <!-- <b-link :to="{name:'auth-forgot-password-v2'}">
                    <small>{{ $t("login.forgot_password") }}</small>
                  </b-link> -->
                </div>
                <validation-provider
                  #default="{ errors }"
                  :name="$t('form.input.password')"
                  rules="required"
                >
                  <b-input-group
                    class="input-group-merge"
                    :class="errors.length > 0 ? 'is-invalid':null"
                  >
                    <b-form-input
                      id="login-password"
                      v-model="password"
                      :state="errors.length > 0 ? false:null"
                      class="form-control-merge"
                      :type="passwordFieldType"
                      name="login-password"
                      :placeholder="$t('form.placeholder.password')"
                    />
                    <b-input-group-append is-text>
                      <feather-icon
                        class="cursor-pointer"
                        :icon="passwordToggleIcon"
                        @click="togglePasswordVisibility"
                      />
                    </b-input-group-append>
                  </b-input-group>
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <!-- checkbox -->
              <b-form-group>
                <b-form-checkbox
                  id="remember-me"
                  v-model="remember"
                  name="checkbox-1"
                >
                  {{ $t('form.input.remember_me') }}
                </b-form-checkbox>
              </b-form-group>

              <!-- submit buttons -->
              <b-button
                type="submit"
                variant="primary"
                block
                @click="handleLogin"
              >
                Sign in
              </b-button>
            </b-form>
          </validation-observer>

          <b-card-text class="text-center mt-2">
            <span>New on our platform? </span>
            <b-link v-on:click="goToCreateAccountPage">
              <span>&nbsp;Create an account</span>
            </b-link>
          </b-card-text>

        </b-col>
      </b-col>
    <!-- /Login-->
    </b-row>
  </div>
</template>

<script>
/* eslint-disable global-require */
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import VuexyLogo from '@core/layouts/components/Logo.vue'
import {
  BRow,
  BCol,
  BLink,
  BFormGroup,
  BFormInput,
  BInputGroupAppend,
  BInputGroup,
  BFormCheckbox,
  BCardText,
  BCardTitle,
  BImg,
  BForm,
  BButton,
} from 'bootstrap-vue'
import { required, email } from '@validations'
import { togglePasswordVisibility } from '@core/mixins/ui/forms'
import { useToast } from "vue-toastification/composition";
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import { useAuth } from '@/composables/auth/useAuth'
import {useRouter} from "@core/utils/utils"
import {computed, ref} from "@vue/composition-api";
import { $themeConfig } from '@themeConfig'

export default {
  components: {
    BRow,
    BCol,
    BLink,
    BFormGroup,
    BFormInput,
    BInputGroupAppend,
    BInputGroup,
    BFormCheckbox,
    BCardText,
    BCardTitle,
    BImg,
    BForm,
    BButton,
    VuexyLogo,
    ValidationProvider,
    ValidationObserver,
  },
  mixins: [togglePasswordVisibility],
  setup() {
    const emailInput = ref('')
    const password = ref('')
    const remember = ref(false)
    const passwordFieldType = ref('password')
    const loginValidation = ref(null)

    const { error, login } = useAuth()
    const toast = useToast()
    const { route, router } = useRouter()

    const passwordToggleIcon = computed(() => {
      return passwordFieldType.value === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    })

    const handleLogin = async () => {
      const success = await loginValidation.value.validate()

      if (success) {
        const response = await login([emailInput.value, password.value, remember.value])
        if (response) {
          const redirect = route.value.query.redirect;
          if (redirect) {
            router.go({ path: redirect});
          } else {
            router.go({ name: 'dashboard' });
          }
        } else {
          toast({
            component: ToastificationContent,
            props: {
              title: error.value,
              icon: 'EditIcon',
              variant: 'danger',
            },
          })
        }
      }
    }

    const goToCreateAccountPage = () => {
      const url = process.env.APP_URL;
      location.href = url  + '/shops/create';
    }

    const { appLogoImage } = $themeConfig.app

    return {
      emailInput,
      password,
      remember,
      passwordFieldType,
      loginValidation,
      passwordToggleIcon,
      login,
      handleLogin,
      appLogoImage,
      goToCreateAccountPage,
    }
  },
  data() {
    return {
      sideImg: require('@/assets/images/pages/login-v2.svg'),
      // validation rules import
      required,
      email,
    }
  },
}
</script>

<style lang="scss">
@import '~@core/scss/vue/pages/page-auth.scss';
</style>
