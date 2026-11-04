<template>

  <div>
    <b-overlay
      :show="loading"
      rounded="sm"
    >
      <validation-observer
        ref="refFormObserver"
      >
        <b-form
          class="list-view"
          @submit.prevent="handleSave()"
        >
          <b-card>
            <b-card-header class="flex-column align-items-start">
              <b-card-title>{{ $t('Import/Export Products') }}</b-card-title>

            </b-card-header>
            <b-card-body>

              <b-card-text class="">
                Upload Excel file for import:
              </b-card-text>

              <validation-provider
                #default="validationContext"
                name="Upload File"
                rules="required|ext:xlsx,jpg"
              >
                <b-form-group
                  class="mb-2"
                >
                  <b-form-file
                    v-model="file"
                    :placeholder="$t('Choose a file or drop it here...')"
                    :drop-placeholder="$t('Drop file here...')"
                    accept=".xlsx, .jpg"
                    :state="getValidationState(validationContext)"
                  />
                  <b-form-invalid-feedback>
                    {{ validationContext.errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>

              <a href="/download/product_import_(ysg_template).xlsx" download>
                <b-button
                  variant="info"
                  type="button"
                >
                  <feather-icon
                    icon="FileTextIcon"
                    class="mr-50"
                  />
                  {{ $t('Template') }}
                </b-button>
              </a>

              <b-button
                variant="info"
                type="button"
                @click="handleExport"
              >
                <feather-icon
                  icon="FileTextIcon"
                  class="mr-50"
                />
                {{ $t('Export') }}
              </b-button>

            </b-card-body>

          </b-card>

          <b-card v-if="results.total">
            <b-card-header class="flex-column align-items-start">
              <b-card-title>{{ $t('Results') }}</b-card-title>

            </b-card-header>
            <b-card-body>

              <b-badge
                class="p-1"
                variant="primary"
                style="font-size:1rem;"
              >{{ $t('Total Products : ') + results.total}}</b-badge>

              <b-badge
                class="ml-1 p-1"
                variant="success"
                style="font-size:1rem;"
              >{{ $t('Success Import : ') + results.success}}</b-badge>

              <b-badge
                class="ml-1 p-1"
                variant="danger"
                style="font-size:1rem;"
              >{{ $t('Failed Import : ') + results.failed}}</b-badge>

              <div class="mt-1" v-if="results.failedReports.length">
                <b-card-text class="text-danger">
                  {{ $t('Error Reports:') }}
                </b-card-text>

                <b-alert class="mt-1 p-1" variant="danger" show>
                  <p
                    v-for="(item, index) in results.failedReports"
                    :key="index"
                  >
                    {{ item }}
                  </p>
                </b-alert>
              </div>

            </b-card-body>

          </b-card>

          <b-button
            variant="primary"
            type="submit"
          >
            <feather-icon
              icon="CheckCircleIcon"
              class="mr-50"
            />
            {{ $t('Import') }}
          </b-button>

          <b-button v-ripple.400="'rgba(186, 191, 199, 0.15)'" variant="secondary"
                    @click="handleCancel">
            <feather-icon
              icon="XCircleIcon"
              class="mr-50"
            />
            Cancel
          </b-button>

        </b-form>
      </validation-observer>
    </b-overlay>
  </div>
</template>

<script>
import {
  BButton,
  BBadge,
  BAlert,
  BForm,
  BFormFile,
  BCard,
  BCardText,
  BCardHeader,
  BOverlay,
  BTooltip,
  VBModal,
  BCardTitle, BCardBody, BFormGroup, BFormInvalidFeedback, BBreadcrumbItem
} from "bootstrap-vue";
import {ValidationObserver, ValidationProvider} from "vee-validate";
import formValidation from "@core/comp-functions/forms/form-validation";
import {required} from "@validations";
import {ref, computed} from '@vue/composition-api';
import Ripple from "vue-ripple-directive";
import ProductResource from "@/api/product";
import {useToast} from 'vue-toastification/composition';
import {useRouter} from "@core/utils/utils";
import axios from '@axios';

export default {
  name: "Import",
  components: {
    BBreadcrumbItem,
    // BSV
    BOverlay,
    BBadge,
    BAlert,
    BForm,
    BButton,
    BTooltip,
    BFormFile,
    BCard,
    BCardBody,
    BCardText,
    BCardHeader,
    BCardTitle,
    BFormGroup,
    BFormInvalidFeedback,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  directives: {
    'b-modal': VBModal,
    Ripple,
  },
  setup() {
    const toast = useToast();

    const {router} = useRouter();
    const productApi = new ProductResource();
    const loading = ref(false);
    const file = ref(null);

    const results = ref({
      total: 0,
      success: 0,
      failed: 0,
      failedReports: []
    });

    const {
      refFormObserver,
      getValidationState,
      resetForm,
    } = formValidation(() => {
      file.value = null
    });

    const submitFile = async function () {
      loading.value = true;
      const formData = new FormData();
      formData.append('uploaded_file', file.value);
      const {success, data, message, errors} = await productApi.importExcel(formData);


      if (success) {

        results.value = data.data;
        toast.success(data.message);
        resetForm();
      } else {

        results.value = errors;

        // disabled because of duplicate error message in task: 27rqn63
        // toast.error(message);
      }
      loading.value = false;
    }

    const handleSave = function () {
      refFormObserver.value.validate().then(success => {
        if (success) {
          submitFile();
        }
      })
    }.bind(refFormObserver, submitFile);

    const handleExport = async function () {
      loading.value = true;

      let productIds = [1, 2, 3];

      const {success, data, message} = await productApi.exportExcel(productIds);

      if (success) {
        window.open(data.filePath, '_blank', 'noreferrer');
      } else {

      }

      loading.value = false;
    }

    const handleCancel = () => {
      router.push({name: 'products'})
    }

    return {
      refFormObserver,
      getValidationState,

      // Form Validators
      required,

      file,

      results: computed(() => results.value),

      loading: computed(() => loading.value),
      handleSave,
      handleCancel,
      handleExport,
    }
  },
}
</script>

<style scoped>

</style>
