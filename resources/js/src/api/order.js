import Resource from '@/api/resource';
import request from '@/utils/requests';

class OrderResource extends Resource {
  constructor() {
    super('orders');
  }

  items(orderId) {
    return request({
      url: '/' + this.uri + '/' + orderId + '/items',
      method: 'get',
    });
  }

  exportExcel(resource) {
    return request({
      url: "/" + this.uri + "/export",
      method: "post",
      data: resource,
    });
  }


}

export { OrderResource as default };
