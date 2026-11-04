import {extend} from 'vee-validate'
import {
  min_value as rule_min_value,
  max_value as rule_max_value,
  max as rule_max,
  double as rule_double
} from 'vee-validate/dist/rules'

export const min_value = extend('min_value', rule_min_value)
export const max_value = extend('max_value', rule_max_value)
export const max = extend('max', rule_max)
export const double = extend('double', rule_double)

export const integer_or_float = extend('integer_or_float', {
  validate: (value, params) => {
    const integer = /^[0-9]+$/;
    const float = /^[0-9]+.?[0-9]{0,2}$/;
    if (String(value).match(integer) !== null) {
      return true;
    } else if (String(value).match(float) !== null) {
      return true;
    }
    return false;
  },
  message: 'The {_field_} field must be a valid decimal with 2 places or less',
});

export const discount_type_percent = extend('discount_percent', {
  params: ['discount_type'],
  validate: (value, {discount_type}) => {
    if (discount_type === 'percent' && value > 100) {
      return false
    }
    return true
  },
  message: 'The Discount field must be 100 or less'
});

export const discount_type_amount = extend('discount_amount', {
  params: ['discount_type', 'price'],
  validate: (value, {discount_type, price}) => {
    if (discount_type === 'amount' && value > parseFloat(price)) {
      return false
    }
    return true
  },
  message: 'The Discount field must be {price} or less'
})

