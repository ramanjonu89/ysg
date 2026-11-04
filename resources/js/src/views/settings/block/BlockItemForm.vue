<template>
  <div>
    <b-modal
        v-model="showItemForm"
        :title="$t('Add Block')"
        :ok-title="$t('Add')"
        cancel-variant="outline-secondary"
        size="lg"
        centered
        modal-class="modal-primary"
        @ok="handleSaveItem"
    >
      <b-overlay
          :show="loading"
          rounded="sm"
      >
        <validation-observer
          ref="refFormObserver"
      >
        <b-form
            @submit.prevent="handleSaveItem()"
        >
          <b-form-group
              label="Active"
              label-for="item-active"
              class="mb-2"
          >
            <b-form-checkbox
                id="item-active"
                switch
                v-model="item.is_enabled"
            />

          </b-form-group>

          <validation-provider
              #default="validationContext"
              name="Title"
              rules="required"
          >
            <b-form-group
                label="Title"
                label-for="item-title"
                class="mb-2"
            >
              <b-form-input
                  id="item-title"
                  :state="getValidationState(validationContext)"
                  trim
                  v-model="item.title"
              />
              <b-form-invalid-feedback>
                {{ validationContext.errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <b-form-group
              label="Description"
              label-for="item-description"
              class="mb-2"
          >
            <b-form-textarea
                id="item-description"
                rows="3"
                trim
                v-model="item.description"
            />
          </b-form-group>

          <validation-provider
              #default="validationContext"
              name="link"
              rules="url"
          >
            <b-form-group
                label="Link"
                label-for="item-options-link"
                class="mb-2"
            >
              <b-form-input
                  id="item-options-link"
                  :state="getValidationState(validationContext)"
                  trim
                  v-model="item.options.link"
              />
              <b-form-invalid-feedback>
                {{ validationContext.errors[0] }}
              </b-form-invalid-feedback>
            </b-form-group>
          </validation-provider>

          <validation-provider
              #default="validationContext"
              name="Image"
              :rules="`${item.image ? '' : 'required'}`"
          >
            <b-media
                no-body
                vertical-align="center"
                class="flex-column flex-md-row"
            >
              <b-media-aside>
                <b-img
                    ref="refPreviewEl"
                    :src="item.image"
                    height="110"
                    width="308"
                    class="rounded mr-2 mb-1 mb-md-0"

                />
              </b-media-aside>
              <b-media-body>
                <small class="text-muted">Required image resolution 1400x500, image size 10mb.</small>
                <div class="d-inline-block">
                  <b-form-file
                      ref="refInputEl"
                      v-model="blogFile"
                      accept=".jpg, .png, .gif"
                      placeholder="Choose file"
                      @input="inputImageRenderer"
                      :state="getValidationState(validationContext)"
                  />
                  <b-form-invalid-feedback>
                    {{ validationContext.errors[0] }}
                  </b-form-invalid-feedback>
                </div>
              </b-media-body>
            </b-media>
          </validation-provider>

        </b-form>
      </validation-observer>
      </b-overlay>
    </b-modal>
  </div>
</template>

<script>
import itemFormService from "./itemFormService";

import {computed, ref, watch} from "@vue/composition-api/dist/vue-composition-api";
import {
  // image
  BMedia,
  BMediaAside,
  BMediaBody,
  BLink,


  BButton,
  BCard,
  BCardBody,
  BCardHeader,
  BCardText,
  BCardTitle,
  BCol,
  BForm, BFormCheckbox, BFormTextarea, BFormFile,
  BFormGroup, BFormInput, BFormInvalidFeedback, BFormSelect, BImg,
  BOverlay,
  BRow, BTooltip, VBModal
} from "bootstrap-vue";
import {ValidationObserver, ValidationProvider} from "vee-validate";
import formValidation from '@/@core/comp-functions/forms/form-validation';
import { required, integer } from '@validations';
import { useInputImageRenderer } from '@/@core/comp-functions/forms/form-utils'
import vSelect from "vue-select";
import Ripple from "vue-ripple-directive";

export default {
  name: "ItemForm",
  components: {
    // BSV
    BOverlay,
    BForm,
    BFormGroup,
    BFormInput,
    BFormTextarea,
    BFormFile,
    BButton,
    BFormInvalidFeedback,
    BFormCheckbox,
    BFormSelect,
    BImg,
    BTooltip,

    //image
    BMedia,
    BMediaAside,
    BMediaBody,
    BLink,

    // Form Validation
    ValidationProvider,
    ValidationObserver,

    // 3rd party
    vSelect,
  },
  directives: {
    'b-modal': VBModal,
    Ripple,
  },
  props: {
  },
  setup(props, { attrs, slots, emit, expose }) {

    const { item, showItemForm, loading, saveItem } = itemFormService();

    const blogFile = ref(null);
    const refInputEl = ref(null)
    const refPreviewEl = ref(null)
    const { inputImageRenderer } = useInputImageRenderer(refInputEl, base64 => { refPreviewEl.value.src = base64 })


    const hasUploadImage = () => {
      return !!(refInputEl.value.files.length);
    }

    const {
      refFormObserver,
      getValidationState,
    } = formValidation(() => {});
    const handleSaveItem = function (bvModalEvt) {
      bvModalEvt.preventDefault();
      refFormObserver.value.validate().then(success => {
        if (success) {
          if (hasUploadImage()) {
            item.value.imageData = refPreviewEl.value.src;
          }
          saveItem();
        }
      })
    };


    return {
      // Form Validation
      refFormObserver,
      getValidationState,

      //image
      refInputEl,
      refPreviewEl,
      inputImageRenderer,
      blogFile,

      //itemFormService
      item,
      showItemForm,
      loading,
      handleSaveItem,
    }
  },
}
</script>

<style scoped>

</style>