import request from '@/utils/requests';

/**
 * Simple RESTful resource class
 */
class Resource {
  constructor(uri) {
    this.uri = uri;
  }
  list(query = '') {
    return request({
      url: '/' + this.uri,
      method: 'get',
      params: query,
    });
  }
  get(id) {
    return request({
      url: '/' + this.uri + '/' + id,
      method: 'get',
    });
  }
  store(resource) {
    return request({
      url: '/' + this.uri,
      method: 'post',
      data: resource,
    });
  }
  update(id, resource) {
    return request({
      url: '/' + this.uri + '/' + id,
      method: 'put',
      data: resource,
    });
  }
  destroy(id) {
    return request({
      url: '/' + this.uri + '/' + id,
      method: 'delete',
    });
  }
  switch(id, field, value) {
    return request({
      url: '/' + this.uri + '/' + id + '/switch',
      method: 'put',
      data: {[field]: value}
    });
  }
  postUpdate(id, resource) {
    return request({
      url: '/' + this.uri + '/' + id + '/edit',
      method: 'post',
      data: resource,
    });
  }
  updateImage(resource) {
    return request({
      url: '/' + this.uri + '/image',
      method: 'post',
      data: resource,
    });
  }
  getBrandsBySeller(query='') {
    return request({
      url: '/' + this.uri,
      method: 'get',
      params: query,
    })
  }
  updateProductPrice(id, data) {
    return request({
      url: '/' + this.uri + '/' + id + '/update_product_price',
      method: 'put',
      data: {
        ['data']: data
      }
    });
  }
  updateProductStock(id, data) {
    return request({
      url: '/' + this.uri + '/' + id + '/update_product_stock',
      method: 'put',
      data: {
        ['data']: data
      }
    });
  }
  updateProductDiscount(id,data){
    return request({
      url: '/' + this.uri + '/' + id + '/update_product_discount',
      method: 'put',
      data: {
        ['data']: data
      }
    });
  }
}

export { Resource as default };
