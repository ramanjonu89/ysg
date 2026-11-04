import Resource from '@/api/resource';
import request from '@/utils/requests';

class ReportResource extends Resource {
  constructor() {
    super('reports');
  }

  summary() {
    return request({
      url: '/' + this.uri + '/summary',
      method: 'get',
    });
  }

  business() {
    return request({
      url: '/' + this.uri + '/business',
      method: 'get',
    });
  }

  ordersChartData(resource) {
    return request({
      url: '/' + this.uri + '/orders-chart-data',
      method: 'post',
      data: resource,
    });
  }
}

export { ReportResource as default };
