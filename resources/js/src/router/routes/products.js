export default [
  // {
  //   path: '/products',
  //   name: 'products',
  //   component: () => import('@/views/product/Index.vue'),
  //   meta: {
  //     pageTitle: 'Products',
  //     breadcrumb: [
  //       {
  //         text: 'Products',
  //       },
  //     ],
  //   },
  // },
  {
    path: '/products',
    redirect: '/products/list',
    meta: {
      pageTitle: 'Products',
      label: 'Products',
    },
    name: 'products',
    component: {
      render(c) {
        return c('router-view')
      }
    },
    children: [
      {
        path: 'list',
        meta: {
          pageTitle: 'Products > List',
          label: 'List',
          breadcrumb: [
            {
              text: 'Products',
            },
            {
              text: 'List',
              active: true,
            },
          ],
          buttons: [
            {
              text: 'Add Product',
              linkTo: 'products.add',
              exact: true,
              active: true,
            },
            {
              text: 'Import/Export Products',
              linkTo: 'products.import-export'
            }
          ],
          resource: 'products',
          action: 'read',
        },
        name: 'products.list',
        component: () => import('@/views/product/Index.vue'),
      },
      {
        path: 'add',
        meta: {
          pageTitle: 'Products > Add',
          label: 'Add',
          breadcrumb: [
            {
              text: 'Products',
              to: { name: 'products.list' }
            },
            {
              text: 'Add',
              active: true,
            },
          ],
          resource: 'products',
          action: 'create',
        },
        name: 'products.add',
        component: () => import('@/views/product/Add.vue'),
      },
      {
        path: 'import-export',
        meta: {
          pageTitle: 'Products > Import/Export',
          label: 'Import/Export',
          breadcrumb: [
            {
              text: 'Products',
              to: { name: 'products.list' }
            },
            {
              text: 'Import/Export',
              active: true,
            },
          ],
          resource: 'products',
          action: 'create',
        },
        name: 'products.import-export',
        component: () => import('@/views/product/Import.vue'),
      },
      {
        path: ':id',
        meta: {
          pageTitle: 'Products > Edit',
          label: 'Edit',
          breadcrumb: [
            {
              text: 'Products',
              to: { name: 'products.list' }
            },
            {
              text: 'Edit',
              active: true,
            },
          ],
          resource: 'products',
          action: 'update',
        },
        name: 'products.edit',
        component: () => import('@/views/product/Edit.vue'),
      },
    ]
  },
]
