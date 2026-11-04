import Resource from '@/api/resource';
import request from '@/utils/requests';

class CouponResource extends Resource {
  constructor() {
    super('coupons');
  }
}

export { CouponResource as default };
