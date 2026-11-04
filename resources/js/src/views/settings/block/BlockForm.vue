<template>
  <div>
    <b-overlay
        :show="loadingBlock"
        rounded="sm"
    >
      <validation-observer
          ref="refFormObserver"
      >
        <b-form
            class="list-view"
            @submit.prevent="block.id != null ? handleUpdateBlock() : handleSaveBlock()"
        >
          <!-- Left Form -->
          <b-card no-body>
            <b-card-header class="flex-column align-items-start">
              <b-card-title>{{ blockTitle }}</b-card-title>
              <b-card-text class="text-muted mt-25">
                Options
              </b-card-text>
            </b-card-header>
            <b-card-body>
              <b-form-group
                  label="Active/Inactive"
                  label-for="block-active"
                  class="mb-2"
              >
                <b-form-checkbox
                    id="block-active"
                    v-model="block.is_enabled"
                    switch
                />

              </b-form-group>

              <b-row>
                <b-col md="6">
                  <validation-provider
                    #default="validationContext"
                    name="Date/Time"
                    rules="required"
                  >
                    <b-form-group
                        label="Date/Time"
                        label-for="datetimer-type"
                        :state="getValidationState(validationContext)"
                    >
                      <b-form-select
                          id="datetimer-type"
                          v-model="block.datetime_type"
                          :options="datetimeTypes"
                          class="mt-1"
                          @change="onDatetimeTypeChange"
                      />

                      <b-form-invalid-feedback :state="getValidationState(validationContext)">
                        {{ validationContext.errors[0] }}
                      </b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>
              </b-row>

              <b-row class="start-date" v-if="showStartDate">
                <b-col md="6">
                  <validation-provider
                    #default="validationContext"
                    name="Start Date"
                    rules="required"
                  >
                    <b-form-group
                      label="Start Date"
                      label-for="start-date"
                    >
                      <b-form-datepicker
                        id="start-date" :placeholder="$t('Start date')"
                        v-model="block.startDate"
                        :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric'}"
                        class="form-control"
                        locale="en-SG"
                        :min="currentDate"
                        :reset-button="true"
                        :state="getValidationState(validationContext)"
                      />

                      <b-form-invalid-feedback>
                        {{ validationContext.errors[0] }}
                      </b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>

                <b-col md="6">
                  <validation-provider
                    #default="validationContext"
                    name="Start Time"
                    rules="required"
                  >
                    <b-form-group
                      label="Start Time"
                      label-for="start-time"
                    >
                      <b-form-timepicker
                        id="start-time" :placeholder="$t('Start Time')"
                        v-model="block.startTime"
                        class="form-control"
                        locale="en-SG"
                        :reset-button="true"
                        :state="getValidationState(validationContext)"
                      />

                      <b-form-invalid-feedback>
                        {{ validationContext.errors[0] }}
                      </b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>
              </b-row>

              <b-row class="end-date" v-if="showEndDate">
                <b-col md="6">
                  <validation-provider
                    #default="validationContext"
                    name="End Date"
                    :rules="`required|date_comparison:${block.startDate}, ${block.datetime_type}`"
                  >
                    <b-form-group
                      label="End Date"
                      label-for="max-qty-end-date"
                    >
                      <b-form-datepicker
                        id="max-qty-end-date" :placeholder="$t('End date')"
                        v-model="block.endDate"
                        :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                        class="form-control"
                        locale="en-SG"
                        :reset-button="true"
                        :min="block.startDate"
                        :state="getValidationState(validationContext)"
                      />
                      <b-form-invalid-feedback>
                        {{ validationContext.errors[0] }}
                      </b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>

                <b-col md="6">
                  <validation-provider
                    #default="validationContext"
                    name="End Time"
                    :rules="`required|time_comparison:${block.startTime}, ${block.startDate}, ${block.endDate}, ${block.datetime_type}`"
                  >
                    <b-form-group
                      label="End Time"
                      label-for="end-time"
                    >
                      <b-form-timepicker
                        id="end-time" :placeholder="$t('End Time')"
                        v-model="block.endTime"
                        class="form-control"
                        locale="en-SG"
                        :reset-button="true"
                        :state="getValidationState(validationContext)"
                      />

                      <b-form-invalid-feedback>
                        {{ validationContext.errors[0] }}
                      </b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>
                </b-col>
              </b-row>

              <validation-provider
                  #default="validationContext"
                  name="Title"
                  rules="required"
              >
                <b-form-group
                    label="Title"
                    label-for="block-title"
                    class="mb-2"
                >
                  <b-form-input
                      id="block-title"
                      v-model="block.title"
                      :state="getValidationState(validationContext)"
                      trim
                  />
                  <b-form-invalid-feedback>
                    {{ validationContext.errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>


              <validation-provider
                  #default="validationContext"
                  name="Block Type"
                  rules="required"
              >

                <b-form-group
                    label="Block Type"
                    label-for="bock-type"
                    :state="getValidationState(validationContext)"
                >
                  <b-form-select
                      id="bock-type"
                      v-model="block.type"
                      :options="blockTypes"
                      class="mt-1"
                      @change="onBlockTypeChange"
                  />

                  <b-form-invalid-feedback :state="getValidationState(validationContext)">
                    {{ validationContext.errors[0] }}
                  </b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>

              <b-button
                  variant="primary"
                  type="submit"
              >
                <feather-icon
                    icon="CheckCircleIcon"
                    class="mr-50"
                />
                Save
              </b-button>

              <b-button v-ripple.400="'rgba(186, 191, 199, 0.15)'" variant="secondary" 
                        @click="handleCancel">
                <feather-icon
                    icon="XCircleIcon"
                    class="mr-50"
                />
                Cancel
              </b-button>

            </b-card-body>
          </b-card>
        </b-form>
      </validation-observer>
    </b-overlay>
  </div>
</template>

<script>
import {
  BOverlay,
  BForm,
  BCard,
  BCardHeader,
  BCardTitle,
  BCardText,
  BCardBody,
  BRow,
  BCol,
  BFormGroup,
  BFormInput,
  BButton,
  BFormCheckbox,
  BFormInvalidFeedback,
  BFormSelect,
  BImg,
  BTooltip, VBModal,
  BFormDatepicker,
  BFormTimepicker
} from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver, extend } from 'vee-validate';
import Ripple from 'vue-ripple-directive';
import vSelect from 'vue-select';
import { required, integer } from '@validations';
import formValidation from '@core/comp-functions/forms/form-validation';
import {reactive, ref, watch} from "@vue/composition-api";

import blockService from "./blockService";

import { useUtils as useI18nUtils } from '@/@core/libs/i18n';
import { useUtils as useAclUtils } from '@/@core/libs/acl';
import {useRouter} from "@core/utils/utils";

extend('date_comparison', {
  validate(value, args) {
    const start_date = args.start_date;
    const datetime_type = args.datetime_type;
    if ((typeof datetime_type === 'string' && datetime_type.trim() !== 'date_range')) {
      return true;
    }
    return new Date(value) >= new Date(start_date);
  },
  params: ['start_date', 'datetime_type'],
  message: 'The End Date must be greater than the Start Date.',
});

extend('time_comparison', {
  validate(value, args) {
    const start_date = args.start_date;
    const end_date = args.end_date;
    const start_time = args.start_time;
    const datetime_type = args.datetime_type;
    
    if ((typeof datetime_type === 'string' && datetime_type.trim() !== 'date_range')) {
      return true;
    }

    if (new Date(end_date) > new Date(start_date)) {
      return true;
    }

    const [startHours, startMinutes] = start_time.split(':').map(Number);
    const [endHours, endMinutes] = value.split(':').map(Number);

    // Compare the times (first compare hours, if equal compare minutes)
    if (endHours > startHours || (endHours === startHours && endMinutes >= startMinutes)) {
      return true;
    }
    
    return false;
  },
  params: ['start_time','start_date', 'end_date', 'datetime_type'],
  message: 'The End Time must be greater than the Start Time.',
});

export default {
  components: {

    // BSV
    BOverlay,
    BForm,
    BCard,
    BCardHeader,
    BCardTitle,
    BCardText,
    BCardBody,
    BRow,
    BCol,
    BFormGroup,
    BFormInput,
    BButton,
    BFormInvalidFeedback,
    BFormCheckbox,
    BFormSelect,
    BImg,
    BTooltip,

    // Form Validation
    ValidationProvider,
    ValidationObserver,

    // 3rd party
    vSelect,

    BFormDatepicker,
    BFormTimepicker
  },
  directives: {
    'b-modal': VBModal,
    Ripple,
  },
  data () {
    return {
      blockTypes: [
        {
          text: 'Slider',
          value: 'microsite_slider'
        },
        {
          text: 'One image',
          value: 'microsite_one_image'
        },
        {
          text: 'Two images',
          value: 'microsite_two_images'
        },
        {
          text: 'Three images',
          value: 'microsite_three_images'
        },
        {
          text: 'Four images',
          value: 'microsite_four_images'
        },
      ],

      datetimeTypes: [
        {
          text: 'Start Date Only',
          value: 'start_date_only'
        },
        {
          text: 'End Date Only',
          value: 'end_date_only'
        },
        {
          text: 'Date Range',
          value: 'date_range'
        },
      ],
      blockTitle: 'Slider'
    }
  },

  methods: {
    onDatetimeTypeChange(value) {
      if (value == 'date_range') {
        this.showStartDate = true;
        this.showEndDate = true;
      } else if(value == 'start_date_only') {
        this.showStartDate = true;
        this.showEndDate = false;
      } else {
        this.showStartDate = false; 
        this.showEndDate = true;
      }
    },

    onBlockTypeChange() {
      this.updateBlockTitle(this.block.type);
    },
    updateBlockTitle(value) {
      const selectedOption = this.blockTypes.find(
        (option) => option.value === value
      );
      this.blockTitle = selectedOption ? selectedOption.text : '';
    }
  },

  watch: {
    'block.type': function(type) {
      this.updateBlockTitle(type);
    }
  },
  
  props: {
  },
  setup(props, { attrs, slots, emit, expose }) {

    const {router} = useRouter();
    const { t } = useI18nUtils();
    const { loadingBlock, block, saveBlock } = blockService();
    const {
      refFormObserver,
      getValidationState,
      resetForm,
      clearForm,
    } = formValidation(() => {});

    const handleSaveBlock = function () {
      refFormObserver.value.validate().then(success => {
        if (success) {
          saveBlock()
        }
      })
    }.bind(refFormObserver, saveBlock);
    
    const handleUpdateBlock = function () {
      refFormObserver.value.validate().then(success => {
        if (success) {
          saveBlock().then(() => {
            router.push({ name: 'settings.blocks' });  
          }).catch(error => {
            console.log(error);
          });
        }
      })
    }.bind(refFormObserver, saveBlock);

    const handleCancel = () => {
      router.push({name: 'settings.blocks'})
    }
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    const currentDate = `${year}-${month}-${day}`;

    block.value.startDate = currentDate;
    block.value.endDate = currentDate;
    block.value.startTime = '0:00:00';
    block.value.endTime = '23:59:59';

    const showStartDate = ref(true);
    const showEndDate = ref(true);
    
    watch(block, () => {
      if (block.value.datetime_type == 'date_range') {
        showStartDate.value = true;
        showEndDate.value = true;
      } else if(block.value.datetime_type == 'start_date_only') {
        showStartDate.value = true;
        showEndDate.value = false;
      } else {
        showStartDate.value = false; 
        showEndDate.value = true;
      }
    })

    return {
      // Form Validation
      refFormObserver,
      getValidationState,
      resetForm,
      clearForm,

      // Form Validators
      required,
      integer,

      //blockService
      loadingBlock,
      block,
      saveBlock,
      handleSaveBlock,
      handleUpdateBlock,
      handleCancel,

      currentDate,
      showStartDate,
      showEndDate
    }
  },
}
</script>

<style lang="scss">
@import '@/@core/scss/vue/libs/vue-select.scss';
@import '@/@core/scss/vue/libs/vue-wizard.scss';
@import '~vue-form-wizard/dist/vue-form-wizard.min.css';
</style>

<style lang="scss" scoped>

</style>