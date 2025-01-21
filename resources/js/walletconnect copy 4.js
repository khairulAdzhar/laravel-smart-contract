import { createAppKit } from '@reown/appkit'
import { EthersAdapter } from '@reown/appkit-adapter-ethers'
import { arbitrum, mainnet, sepolia } from '@reown/appkit/networks'
import { BrowserProvider, Contract } from 'ethers'

const projectId = '3555b62326bd83cf91146427f3a9a340'
const networks = [arbitrum, mainnet, sepolia]
const metadata = {
  name: 'xBUG WalletConnect',
  description: 'xBUG WalletConnect Gateway',
  url: 'https://blockchain.xbug.online',
  icons: ['https://xbug.online/assets/images/logo.png']
}

// Inisialisasi AppKit
export const appKit = createAppKit({
  adapters: [new EthersAdapter()],
  networks,
  metadata,
  projectId,
  features: {
    analytics: true
  }
})

// Opsional: helper function untuk dapatkan signer Ethers
export async function getEthersSigner() {
  const walletProvider = appKit.getWalletProvider()
  if (!walletProvider) {
    throw new Error("Wallet belum terkoneksi. Silakan klik 'Connect Wallet' terlebih dahulu.")
  }
  const ethersProvider = new BrowserProvider(walletProvider);
  const signer = await ethersProvider.getSigner();
  return signer
}
