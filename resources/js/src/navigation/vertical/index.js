export default [
  {
    title: 'Dashboard',
    route: 'dashboard',
    icon: 'HomeIcon',
    resource: 'dashboard',
    action: 'manage',
  },
  // {
  //   title: 'Access Control',
  //   route: 'access-control',
  //   icon: 'ShieldIcon',
  //   resource: 'ACL',
  //   action: 'read',
  // },
  {
    title: 'Products',
    icon: 'AwardIcon',
    route: { name: 'products.list' },
    resource: 'products',
    action: 'read',
  },
  {
    title: 'Orders',
    route: 'orders',
    icon: 'ShoppingCartIcon',
    resource: 'orders',
    action: 'read',
  },
  {
    title: 'Vouchers',
    icon: 'TagIcon',
    route: { name: 'coupons' },
    resource: 'coupons',
    action: 'read',
  },
  {
    title: 'Storefront',
    icon: 'SettingsIcon',
    children: [
      {
        title: 'Featured Product',
        route: { name: 'settings.feature-products.list' },
        resource: 'settings.feature-products',
        action: 'read',
      },
      {
        title: 'Infocubes',
        route: { name: 'settings.blocks.list' },
        resource: 'settings.blocks',
        action: 'read',
      },
    ],
  },
]
