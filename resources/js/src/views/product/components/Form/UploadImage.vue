<template>
  <div>
    <validation-provider
        :vid="`image-${randomId}`"
        name="Image"
        :rules="imageRules"
        v-slot="{ validate, errors }"
    >
      <div class="position-relative">
        <b-img
            :id="`image-`+randomId"
            ref="refPreviewEl"
            :src="imgSrc"
            class="rounded w-100 cursor-pointer"
            :class="`${errors[0] ? 'border-danger' : ''}`"
            @click="$refs.refInputEl.click()"
        />
        <b-tooltip
            title="Edit"
            class="cursor-pointer"
            :target="`image-`+randomId"
        />
        <input
            ref="refInputEl"
            type="file"
            @input="inputImageRenderer"
            @change="validate"
            class="d-none"
        >
        <!--remove button-->
        <div v-if="removeImage!==false" class="position-absolute" style="top: 10px; right: 10px;">
          <b-button :id="`remove-image-`+randomId" v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                    variant="outline-danger"
                    class="btn-icon rounded-circle"
                    @click="removeImage(index)"
          >
            <feather-icon
                icon="XIcon"
                class="cursor-pointer"
                size="16"
            />
          </b-button>
          <b-tooltip
              title="Remove"
              class="cursor-pointer"
              :target="`remove-image-`+randomId"
          />
        </div>

      </div>
      <b-form-invalid-feedback class="d-block">
        {{ errors[0] }}
      </b-form-invalid-feedback>
    </validation-provider>
  </div>
</template>

<script>
import {BButton, BImg, BTooltip, BFormInvalidFeedback, VBModal} from "bootstrap-vue";

import { ref, reactive, computed, watch, nextTick } from '@vue/composition-api';
import formValidation from "@core/comp-functions/forms/form-validation";
import Ripple from "vue-ripple-directive";
import {useInputImageRenderer} from "@core/comp-functions/forms/form-utils";
import { required, integer, image, ext, dimensions, size } from '@validations';
import {ValidationObserver, ValidationProvider} from "vee-validate";

export default {
  name: "UploadImage",
  components: {
    BImg,
    BButton,
    BTooltip,
    BFormInvalidFeedback,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  directives: {
    'b-modal': VBModal,
    Ripple,
  },
  props: {
    index: {
      default: 0,
      type: Number
    },
    img: String,
    placeHolder: {
      default: '/assets/seller_center/images/1000x1000.png',
      type: String,
    },
    editImage: Function,
    removeImage: {
      default: false,
      type: [Function, Boolean],
    },
    rules: Object,
    currentObject: Object,
  },
  setup (props) {
    const randomId = btoa(+new Date).substr(-7, 5);

    const refInputEl = ref(null);
    const refPreviewEl = ref(null);
    const handleEdit = function (base64) {
      refPreviewEl.value.src = base64;
      props.editImage(base64, props.index, props.currentObject);
    };
    const { inputImageRenderer } = useInputImageRenderer(refInputEl, base64 => handleEdit(base64))

    const {
      getValidationState,
    } = formValidation(() => {});

    const defRules = {
      ext: ['jpg', 'jpeg', 'png'],
      size: 1000,
    };
    //
    let imageRules = {...defRules, ...props.rules};
    if ( imageRules.required && (props.img === undefined || props.img.trim === '')) {
      imageRules.required = true;
    } else {
      imageRules.required = false;
    }

    const imgSrc = computed(() => {
      if (props.img === '' || typeof(props.img) === "undefined" || props.img === null) {
        return props.placeHolder
      } else if (props.img.match(/data.*/)) {
        return props.img;
      } else {
        return '/'+props.img;
      }
    });

    return {
      randomId,

      getValidationState,

      // Form Validators
      required,

      //image
      imageRules: imageRules,

      refInputEl,
      refPreviewEl,
      inputImageRenderer,
      imgSrc,
    }
  },
}
</script>

<style scoped>

</style>
