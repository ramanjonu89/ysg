<template>
  <div>
    <b-row>
      <b-col cols="12" lg="4">
        <block-form
        ></block-form>
      </b-col>

      <b-col cols="12" lg="8">
        <item-list></item-list>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import { ref, computed, watch, nextTick } from '@vue/composition-api';
import {
  BRow,
  BCol,
} from 'bootstrap-vue'
import blockForm from "./BlockForm";
import itemList from "./BlockItemList";
import blockService from './blockService';
import {useRouter} from '@/@core/utils/utils';

export default {
  name: 'BlockAdd',
  components: {
    blockForm,
    itemList,
    // BSV
    BRow,
    BCol,
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
    }
  },
  props: {

  },
  setup () {
    const {route, router} = useRouter();
    const { addBlock, loadBlock } = blockService();

    addBlock()
    return {
    }
  },
}
</script>

<style lang="scss">
</style>

<style lang="scss" scoped>
</style>