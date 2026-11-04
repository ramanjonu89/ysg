<template>
    <th v-if="isHeader" class="vuetable-th-component-product-name text-justify" v-html="title"></th>
    <td v-else class="vuetable-td-component-product-name component-product-name">
        <div class="custom-product-name">
            <b-row class="d-flex align-items-center">
                <b-col sm="3 thumbnail-img">
                    <b-img :width="100" thumbnail fluid :src="thumbUrl(rowData.thumbnail_img, rowData.id)"
                        :alt="`Image ${rowData.id}`"></b-img>
                </b-col>
                <b-col sm="8 product-name">
                    <div class="">
                        <span>{{ rowData.name }}</span>
                        <p class="item-sku mb-0"> SKU: {{ rowData.sku }}</p>
                    </div>
                </b-col>
            </b-row>
        </div>
    </td>
</template>
<script>
import {
    BCol,
    BImg,
    BRow,
} from 'bootstrap-vue'
import VuetableFieldMixin from 'vuetable-2/src/components/VuetableFieldMixin.vue'
export default {
    name: 'vuetable-field-product-name',
    mixins: [VuetableFieldMixin],
    components: {
        BCol,
        BImg,
        BRow
    },
    setup() {
        const thumbUrl = (file, id) => {
            if (file.indexOf('uploads') !== -1 && file.indexOf('products') !== -1) {
                return file.startsWith('/') ? file : '/' + file;
            } else {
                return `/uploads/products/${id}/thumbnail/${file}`
            }
        }

        return {
            thumbUrl
        }
    }
}
</script>
<style lang="scss">
    .custom-product-name .thumbnail-img {
        padding-left: 2rem !important;
        padding-right: 0.5rem !important;
    }

    .custom-product-name .product-name {
        padding-left: 0rem !important;
    }

    .component-product-name {
        padding-left: 0 !important;
        padding-right: 0 !important;
        width: 35% !important;
    }
</style>