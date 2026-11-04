import Resource from '@/api/resource';
import request from '@/utils/requests';

class CategoryResource extends Resource {
  constructor() {
    super('categories');
  }

  getCategoriesBySeller() {
    return request({
      url: '/' + this.uri + '/by-seller',
      method: 'GET',
    });
  }

  tree() {
    return request({
      url: '/' + this.uri + '/tree',
      method: 'GET',
    });
  }

}

export { CategoryResource as default };
