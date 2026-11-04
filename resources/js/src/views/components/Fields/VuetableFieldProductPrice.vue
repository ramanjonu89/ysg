<template>
    <th v-if="isHeader" class="vuetable-th-component-product-price pl-0 pr-0">
        <b-row class="header">
            <b-col sm="4" class="classification pr-0">Classification</b-col>
            <b-col sm="2" class="original-rice">Original Price</b-col>
            <b-col sm="2" class="discount p-0">Discount</b-col>
            <b-col sm="2" class="pl-0 selling-price">Selling Price</b-col>
            <b-col sm="2" class="pl-0 stock">Stock</b-col>
        </b-row>
    </th>
    <td v-else class="vuetable-td-component-product-price component-product-price">
        <div v-if="rowData['stocks'].length == 0" class="custom-actions">
            <b-row class="d-flex align-items-center">
                <b-col sm="4">
                    <span>___</span>
                </b-col>
                <b-col sm="2" class="pl-0">
                    <div class="d-flex align-items-center" @mouseenter="is_edit_price = true" @mouseleave="is_edit_price = false">
                        <span class="display-price">{{ rowData['display_price'] }}</span>
                        <b-button
                            v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                            variant="flat-primary"
                            class="btn-icon rounded-circle"
                            @click="getModalUpdatePrice(rowData)"
                            :id="`${rowData.id}-edit`"
                            v-if="is_edit_price"
                        >
                        <feather-icon icon="EditIcon"/>
                        </b-button>
                    </div>
                </b-col>
                <b-col sm="2" class="pl-0">
                    <div class="d-flex align-items-center" @mouseenter="is_edit_discount = true" @mouseleave="is_edit_discount = false">
                        <span class="display-price">{{ rowData['display_discount'] }}</span>
                        <b-button
                            v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                            variant="flat-primary"
                            class="btn-icon rounded-circle"
                            @click="getModalUpdateDiscount(rowData)"
                            :id="`${rowData.id}-edit`"
                            v-if="is_edit_discount"
                        >
                        <feather-icon icon="EditIcon"/>
                        </b-button>
                    </div>
                </b-col>
                <b-col sm="2" class="pl-0">
                    <div class="text-center">
                        <span class="display-price">{{ rowData['display_discount_price'] }}</span>
                    </div>
                </b-col>
                <b-col sm="2" class="pl-0">
                    <div class="d-flex align-items-center" @mouseenter="is_edit_stock = true" @mouseleave="is_edit_stock = false"> 
                        <span class="pl-2">{{ rowData['stock'] }}</span>
                        <b-button
                            v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                            variant="flat-primary"
                            class="btn-icon rounded-circle"
                            @click="getModalUpdateStock(rowData)"
                            :id="`${rowData.id}-edit`"
                        v-if="is_edit_stock"
                        >
                        <feather-icon icon="EditIcon"/>
                        </b-button>
                    </div>
                </b-col>
            </b-row>
        </div>
        <div v-else class="custom-actions">
            <div v-for="( item, index ) in rowData['stocks']" :key="item.id"> 
                <b-row class="d-flex align-items-center" :class="{'dart-line': index > 0}">
                    <b-col sm="4" class="pr-0">
                        <div class="">
                            <span>{{ item.variant }}</span>
                            <p class="item-sku mb-0"> SKU: {{ item.sku  }}</p>
                        </div>
                    </b-col>
                    <b-col sm="2" class="pl-0">
                        <div class="d-flex align-items-center" @mouseenter="is_edit_price = true" @mouseleave="is_edit_price = false">
                            <span class="display-price"> {{ item.display_price }} </span>
                            <b-button
                                v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                                variant="flat-primary"
                                class="btn-icon rounded-circle"
                                @click="getModalUpdatePrice(rowData)"
                                :id="`${item.id}-edit`"
                                v-if="is_edit_price"
                            >
                            <feather-icon icon="EditIcon"/>
                            </b-button>
                        </div>
                    </b-col>
                    <b-col sm="2" class="pl-0">
                        <div class="d-flex align-items-center"  @mouseenter="is_edit_discount = true" @mouseleave="is_edit_discount = false">
                            <span class="display-price">{{ item.display_discount }}</span>
                            <b-button
                                v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                                variant="flat-primary"
                                class="btn-icon rounded-circle"
                                @click="getModalUpdateDiscount(rowData)"
                                :id="`${item.id}-edit`"
                                v-if="is_edit_discount"
                            >
                            <feather-icon icon="EditIcon"/>
                            </b-button>
                        </div>
                    </b-col>
                    <b-col sm="2" class="pl-0">
                        <div class="text-center">
                            <span class="display-price">{{ item.display_discount_price }}</span>
                        </div>
                    </b-col>
                    <b-col sm="2" class="pl-0 pr-0">
                        <div class="d-flex align-items-center"  @mouseenter="is_edit_stock = true" @mouseleave="is_edit_stock = false">
                        <span class="pl-2"> {{ item.display_quantity }} </span>
                        <b-button
                            v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                            variant="flat-primary"
                            class="btn-icon rounded-circle"
                            @click="getModalUpdateStock(rowData)"
                            :id="`${item.id}-edit`"
                            v-if="is_edit_stock"
                        >
                        <feather-icon icon="EditIcon"/>
                        </b-button>
                        </div>
                    </b-col>
                </b-row>
            </div>
        </div>
    </td>
</template>
<script>
    import {
        BButton,
        BCol,
        BRow,
    } from 'bootstrap-vue'
    import VuetableFieldMixin from 'vuetable-2/src/components/VuetableFieldMixin.vue'
    
    import Ripple from 'vue-ripple-directive';

    export default {
        name: 'vuetable-field-product-price',
        mixins: [VuetableFieldMixin],
        components: {
            BButton,
            BCol,
            BRow
        },
        directives: {
            Ripple
        },
        data() {
            return {
                price: '',
                is_edit_price: false,
                is_edit_stock: false,
                is_edit_discount: false
            }
        },
        methods: {
            getModalUpdatePrice(data) {
                const type_update = this.rowField.updatePrice;
                this.rowField.getModal(data, type_update);
            },

            getModalUpdateStock(data) {
                const type_update = this.rowField.updateStock;
                this.rowField.getModal(data, type_update);
            },

            getModalUpdateDiscount(data) {
                const type_update = this.rowField.updateDiscount;
                this.rowField.getModal(data, type_update);
            }
        }
    }
</script>
<style lang="scss">
    .selling-price,
    .stock {
        padding: 0rem 0.3rem !important;
    }

    .original-rice {
        padding-left: 0rem !important;
        padding-right: 0.3rem !important;
    }

    .component-product-price {
        padding-left: 0 !important;
        padding-right: 0 !important;
        width: 65% !important;
    }
</style>
