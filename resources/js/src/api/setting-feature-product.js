import Resource from '@/api/resource';
import request from '@/utils/requests';

class SettingFeatureProduct extends Resource {
  constructor() {
    super('settings/feature-products');
  }

  updateSort(id, data) {
    return request({
      url: '/' + this.uri + '/' + id + '/update-sort',
      method: 'put',
      data: data,
    });
  }

  searchProduct(data) {
    return request({
      url: '/' + this.uri + '/search',
      method: 'post',
      data: data,
    });
  }
}

export { SettingFeatureProduct as default };
