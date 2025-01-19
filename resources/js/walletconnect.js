import { createAppKit } from '@reown/appkit/react';
import { EthersAdapter } from '@reown/appkit-adapter-ethers';
import { arbitrum, mainnet, sepolia } from '@reown/appkit/networks';
import { ethers } from 'ethers';

// Konfigurasi AppKit
const projectId = '3555b62326bd83cf91146427f3a9a340';
const networks = [arbitrum, mainnet, sepolia];

const metadata = {
  name: 'My Laravel App',
  description: 'Laravel WalletConnect Integration',
  url: 'https://my-laravel-app.com',
  icons: ['https://my-laravel-app.com/icon.png']
};

// Simpan provider dan signer untuk kegunaan global
let provider, signer, userAccount;

const appKit = createAppKit({
  adapters: [new EthersAdapter()],
  networks,
  metadata,
  projectId,
  features: {
    analytics: true
  },
  onConnect: async (adapter) => {
    provider = new ethers.providers.Web3Provider(adapter.provider);
    signer = provider.getSigner();
    userAccount = await signer.getAddress();
    console.log('Wallet Connected:', userAccount);
  },
  onDisconnect: () => {
    provider = null;
    signer = null;
    userAccount = null;
    console.log('Wallet Disconnected');
  }
});

// Export pembolehubah untuk digunakan di view
export { provider, signer, userAccount };
