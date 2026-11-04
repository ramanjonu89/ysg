<template>
  <section>
    <b-card>
      <b-card-header class="flex-column align-items-start">
        <b-card-title>Product Description</b-card-title>
      </b-card-header>
      <b-card-body>
        <validation-provider
          :vid="`product-description`"
          name="Product Description"
          rules="required|description_min_length:50"
          v-slot="{ validate, errors }"
        >
          <jodit-editor
            v-model="product.description"
            :config="editorConfig"
            :class="`${errors[0] ? 'border-danger rounded' : ''}`"
          />

          <b-form-invalid-feedback class="d-block">
            {{ errors[0] }}
          </b-form-invalid-feedback>
        </validation-provider>

        <b-card-text class="mt-1">
          <small>
            {{
              $t(
                "To prevent performance issues, please ensure the following for Image:"
              )
            }}
          </small>
          <ol>
            <li>
              <small>{{ $t("Image Format: JPG") }}</small>
            </li>
            <li>
              <small>{{ $t("Image Sizing: 800px*800px") }}</small>
            </li>
            <li>
              <small>{{ $t("Image Size: 150kb (max)") }}</small>
            </li>
          </ol>
        </b-card-text>
      </b-card-body>
    </b-card>
  </section>
</template>

<script>
import {
  BButton,
  BCard,
  BCardBody,
  BCardHeader,
  BCardText,
  BCardTitle,
  BFormInvalidFeedback,
  BImg,
  BTooltip,
} from "bootstrap-vue";

import 'jodit/build/jodit.min.css'
import { JoditEditor } from 'jodit-vue'

import { reactive } from "@vue/composition-api/dist/vue-composition-api";
import { computed, inject } from "@vue/composition-api";
import { extend, ValidationProvider } from "vee-validate";
import formValidation from "@core/comp-functions/forms/form-validation";

export const description_min_length = extend("description_min_length", {
  params: ["min_length"],
  validate: (value, { min_length }) => {
    let tempDiv = document.createElement("div");
    tempDiv.innerHTML = value;
    let textContent = tempDiv.textContent || tempDiv.innerText;
    tempDiv = null;

    if (textContent.length < min_length) {
      return false;
    }
    return true;
  },
  message: "The {_field_} field must be more than {min_length} characters",
});

export default {
  name: "ProductDescription",
  components: {
    BButton,
    BTooltip,
    BImg,
    ValidationProvider,
    BFormInvalidFeedback,
    // BSV
    BCard,
    BCardHeader,
    BCardTitle,
    BCardText,
    BCardBody,

    JoditEditor,
  },
  setup() {
    const editorConfig = reactive({
      readonly: false,
      height: 400,
      uploader: {
        insertImageAsBase64URI: true
      },
      buttons: [
        'source', '|',
        'bold', 'italic', 'underline', 'strikethrough', '|',
        'superscript', 'subscript', '|',
        'ul', 'ol', '|',
        'outdent', 'indent', '|',
        'font', 'fontsize', 'brush', 'paragraph', '|',
        'image', 'video', 'table', 'link', '|',
        'align', 'undo', 'redo', '|',
        'hr', 'eraser', 'copyformat'
      ],
      language: 'en'
    });

    const product = inject("product");

    const { getValidationState } = formValidation(() => {});

    return {
      editorConfig,
      product,

      getValidationState,
    };
  },
};
</script>

<style>
.jodit-container iframe {
  pointer-events: none;
}
</style>
