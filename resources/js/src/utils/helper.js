import moment from "moment";
import Currency from '@/utils/currency';

// todo: format will depend on locale
const DateTimeFormat = 'DD-MM-YYYY HH:mm:ss';
const DateFormat = 'DD-MM-YYYY';
const TimeFormat = 'HH:mm:ss';

export function parseDateTime(dateTime) {
  return moment(dateTime).format(DateTimeFormat);
}

export function parseDate(dateTime) {
  return moment(dateTime).format(DateFormat);
}

export function parseTime(dateTime) {
  return moment(dateTime).format(TimeFormat);
}

export function titleCase(str) {
  return str.replace (/^[-_]*(.)/, (_, c) => c.toUpperCase())       // Initial char (after -/_)
    .replace (/[-_]+(.)/g, (_, c) => ' ' + c.toUpperCase()) // First char after each -/_;
}

export function getDeliveryBadgeHtml(status, shippingType = null) {
  const className = getDeliveryBadge(status);
  const textStatus = getTextStatus(status, shippingType);
  return `<span class="badge badge-${className}">${textStatus}</span>`;
}

export function getDeliveryBadge (status) {
  switch (status) {
    case 'pending': return 'warning'
    case 'on_review': return 'primary'
    case 'on_delivery': return 'info'
    case 'delivered': return 'success'
    case 'cancelled': return 'danger'
    default: 'warning'
  }
}

export function getTextStatus(status, shippingType = null) {
  if (status === 'on_review' && shippingType === 'self_collection') {
    return titleCase('Preparing Order');
  }

  if (status === 'on_delivery' && shippingType === 'self_collection') {
    return titleCase('Ready for collection');
  }

  if (status === 'delivered' && shippingType === 'self_collection') {
    return titleCase('Collected');
  }

  return titleCase(status);
}

export function getPaymentBadgeHtml(status) {
  const className = getPaymentBadge(status);
  return `<span class="badge badge-${className}">${status}</span>`;
}

export function getPaymentBadge (status) {
  switch (status) {
    case 'paid': return 'success'
    case 'unpaid': return 'warning'
    default: 'warning'
  }
}

export function getDiscount(discountAmount, discountType) {
  if (discountType === 'percent') {
    return discountAmount + '%';
  }

  return Currency.formatPrice(discountAmount);
}
export function isEmptyObj(obj) {
  for (let i in obj) return false;
  return true;
}
