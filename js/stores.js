let STORES = [];
let STORES_LOADED = false;

const DEFAULT_STORES = [
  {
    id: 'store-margonda',
    name: 'Warung Madura Margonda',
    address: 'Jl. Margonda Raya No. 45, Depok',
    distance: '0,8 km',
    hours: '24 Jam',
    phone: '0812-3456-7801',
    status: 'open',
    lat: -6.363924,
    lng: 106.831777
  },
  {
    id: 'store-tole',
    name: 'Warung Madura Tole Iskandar',
    address: 'Jl. Tole Iskandar No. 12, Depok',
    distance: '1,4 km',
    hours: '24 Jam',
    phone: '0812-3456-7802',
    status: 'open',
    lat: -6.394924,
    lng: 106.821777
  },
  {
    id: 'store-kartini',
    name: 'Warung Madura Kartini',
    address: 'Jl. Kartini No. 8, Depok',
    distance: '2,1 km',
    hours: '24 Jam',
    phone: '0812-3456-7803',
    status: 'open',
    lat: -6.373924,
    lng: 106.841777
  },
  {
    id: 'store-juanda',
    name: 'Warung Madura Juanda',
    address: 'Jl. Ir. H. Juanda No. 30, Depok',
    distance: '3,0 km',
    hours: '24 Jam',
    phone: '0812-3456-7804',
    status: 'open',
    lat: -6.383924,
    lng: 106.851777
  }
];

function loadStores() {
  const stored = localStorage.getItem('klikMaduraStores');
  if (stored) {
    STORES = JSON.parse(stored);
  } else {
    STORES = [...DEFAULT_STORES];
    saveStores();
  }
  
  STORES_LOADED = true;
  return STORES;
}

function saveStores() {
  localStorage.setItem('klikMaduraStores', JSON.stringify(STORES));
}

function getStores() {
  if (!STORES_LOADED) {
    loadStores();
  }
  return STORES;
}

function getStoreById(id) {
  return STORES.find(s => s.id === id);
}

function addStore(storeData) {
  const newStore = {
    id: generateStoreId(),
    name: storeData.name,
    address: storeData.address,
    distance: storeData.distance || '-',
    hours: storeData.hours || '24 Jam',
    phone: storeData.phone || '',
    status: storeData.status || 'open',
    lat: storeData.lat || 0,
    lng: storeData.lng || 0
  };
  
  STORES.push(newStore);
  saveStores();
  return newStore;
}

function updateStore(id, updates) {
  const index = STORES.findIndex(s => s.id === id);
  
  if (index !== -1) {
    STORES[index] = { ...STORES[index], ...updates };
    saveStores();
    return STORES[index];
  }
  
  return null;
}

function deleteStore(id) {
  const index = STORES.findIndex(s => s.id === id);
  if (index !== -1) {
    const deleted = STORES.splice(index, 1)[0];
    saveStores();
    return deleted;
  }
  return null;
}

function toggleStoreStatus(id) {
  const store = getStoreById(id);
  
  if (store) {
    store.status = store.status === 'open' ? 'closed' : 'open';
    saveStores();
    return store;
  }
  
  return null;
}

function generateStoreId() {
  const timestamp = Date.now();
  const random = Math.floor(Math.random() * 1000);
  return `store-${timestamp}-${random}`;
}

function getOpenStores() {
  return STORES.filter(s => s.status === 'open');
}

function getStoresCount() {
  return {
    total: STORES.length,
    open: STORES.filter(s => s.status === 'open').length,
    closed: STORES.filter(s => s.status === 'closed').length
  };
}

loadStores();
