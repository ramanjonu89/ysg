import Resource from '@/api/resource';
import request from "@/utils/requests";

class ProductResource extends Resource {
  constructor() {
    super("products");
  }

  getSelectOptions() {
    return request({
      url: "/" + this.uri + "/select-options",
      method: "get",
    });
  }

  getAllowedAttributes(id = null) {
    let urlString = "/" + this.uri + "/allowed-attributes";
    if (id !== null) {
      urlString = "/" + this.uri + "/" + id + "/allowed-attributes";
    }
    return request({
      url: urlString,
      method: "get",
    });
  }

  importExcel(resource) {
    return request({
      url: "/" + this.uri + "/import",
      method: "post",
      data: resource,
      timeout: 60000,
    });
  }

  exportExcel(resource) {
    return request({
      url: "/" + this.uri + "/export",
      method: "post",
      data: resource,
      timeout: 60000,
    });
  }
  updateSort(id, data) {
    return request({
      url: "/" + this.uri + "/update-sort?order_by=frontend_sorting",
      method: "put",
      data: data,
    });
  }
  getListForVoucherByIds(query) {
    return request({
      url: "/" + this.uri + "/for-voucher",
      method: "get",
      params: query,
    });
  }
  update(id, resource) {
    let form_data = new FormData();

    for (let key in resource) {
      if (resource[key] == null) {
        continue;
      }
      if (resource[key] instanceof Array) {
        form_data.append(key, JSON.stringify(resource[key]));
      } else {
        form_data.append(key, resource[key]);
      }
      if (key === "photos") {
        resource[key].forEach((image) => {
          if (image instanceof File) {
            form_data.append("added_images[]", image);
            form_data.append("photos[]", image.name);
          } else {
            form_data.append("photos[]", image);
          }
        });
        form_data.delete(key);
      }
    }
    form_data.append("_method", "PUT"); // hack PUT API Laravel
    return request({
      url: "/" + this.uri + "/" + id,
      method: "POST",
      data: form_data,
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });
  }
  store(resource) {
    let form_data = new FormData();

    for (let key in resource) {
      if (key === "photos") {
        resource[key].forEach((image) => {
          form_data.append("photos[]", image);
        });
      }

      if (resource[key] instanceof Array) {
        form_data.append(key, JSON.stringify(resource[key]));
      } else {
        form_data.append(key, resource[key]);
      }
    }
    return request({
      url: "/" + this.uri,
      method: "post",
      data: form_data,
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });
  }
}

export { ProductResource as default };
