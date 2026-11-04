import Resource from '@/api/resource';
import request from '@/utils/requests';

class BrandResource extends Resource {
  constructor() {
    super('brands');
  }

  getBrandsBySeller() {
    return request({
      url: '/' + this.uri + '/by-seller',
      method: 'GET',
    });
  }

  getBrandOptions() {
    return request({
      url: '/' + this.uri + '/options',
      method: 'GET',
    });
  }

}

export { BrandResource as default };
