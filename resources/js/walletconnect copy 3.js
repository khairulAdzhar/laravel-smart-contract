// resources/js/walletconnect.js
import { createAppKit } from '@reown/appkit'
import { EthersAdapter } from '@reown/appkit-adapter-ethers'
import { arbitrum, mainnet, sepolia } from '@reown/appkit/networks'
import { BrowserProvider, Contract } from 'ethers'

// Inisialisasi AppKit
const projectId = '3555b62326bd83cf91146427f3a9a340'
const networks = [arbitrum, mainnet, sepolia]

const metadata = {
  name: 'xBUG WalletConnect',
  description: 'xBUG WalletConnect Gateway',
  url: 'https://blockchain.xbug.online',
  icons: ['https://xbug.online/assets/images/logo.png']
}

export const appKit = createAppKit({
  adapters: [new EthersAdapter()],
  networks,
  metadata,
  projectId,
  features: { analytics: true }
})

// Contoh: ABI & alamat kontrak
const contractABI = [{
  "anonymous": false,
  "inputs": [{
          "indexed": false,
          "internalType": "uint256",
          "name": "id",
          "type": "uint256"
      },
      {
          "indexed": false,
          "internalType": "address",
          "name": "user",
          "type": "address"
      },
      {
          "indexed": false,
          "internalType": "string",
          "name": "name",
          "type": "string"
      },
      {
          "indexed": false,
          "internalType": "string",
          "name": "createdAt",
          "type": "string"
      },
      {
          "indexed": false,
          "internalType": "string",
          "name": "link",
          "type": "string"
      },
      {
          "indexed": false,
          "internalType": "string",
          "name": "enrollmentPrice",
          "type": "string"
      },
      {
          "indexed": false,
          "internalType": "string",
          "name": "place",
          "type": "string"
      },
      {
          "indexed": false,
          "internalType": "string",
          "name": "contentType",
          "type": "string"
      },
      {
          "indexed": false,
          "internalType": "string",
          "name": "provider",
          "type": "string"
      },
      {
          "indexed": false,
          "internalType": "string",
          "name": "organizationName",
          "type": "string"
      },
      {
          "indexed": false,
          "internalType": "string",
          "name": "userName",
          "type": "string"
      },
      {
          "indexed": false,
          "internalType": "uint256",
          "name": "timestamp",
          "type": "uint256"
      }
  ],
  "name": "ContentAdded",
  "type": "event"
},
{
  "inputs": [{
          "internalType": "string",
          "name": "_name",
          "type": "string"
      },
      {
          "internalType": "string",
          "name": "_createdAt",
          "type": "string"
      },
      {
          "internalType": "string",
          "name": "_link",
          "type": "string"
      },
      {
          "internalType": "string",
          "name": "_enrollmentPrice",
          "type": "string"
      },
      {
          "internalType": "string",
          "name": "_place",
          "type": "string"
      },
      {
          "internalType": "string",
          "name": "_contentType",
          "type": "string"
      },
      {
          "internalType": "string",
          "name": "_provider",
          "type": "string"
      },
      {
          "internalType": "string",
          "name": "_organizationName",
          "type": "string"
      },
      {
          "internalType": "string",
          "name": "_userName",
          "type": "string"
      }
  ],
  "name": "addContent",
  "outputs": [],
  "stateMutability": "nonpayable",
  "type": "function"
},
{
  "inputs": [],
  "name": "contentCount",
  "outputs": [{
      "internalType": "uint256",
      "name": "",
      "type": "uint256"
  }],
  "stateMutability": "view",
  "type": "function"
},
{
  "inputs": [{
      "internalType": "uint256",
      "name": "",
      "type": "uint256"
  }],
  "name": "contents",
  "outputs": [{
          "internalType": "uint256",
          "name": "id",
          "type": "uint256"
      },
      {
          "internalType": "address",
          "name": "user",
          "type": "address"
      },
      {
          "internalType": "string",
          "name": "name",
          "type": "string"
      },
      {
          "internalType": "string",
          "name": "createdAt",
          "type": "string"
      },
      {
          "internalType": "string",
          "name": "link",
          "type": "string"
      },
      {
          "internalType": "string",
          "name": "enrollmentPrice",
          "type": "string"
      },
      {
          "internalType": "string",
          "name": "place",
          "type": "string"
      },
      {
          "internalType": "string",
          "name": "contentType",
          "type": "string"
      },
      {
          "internalType": "string",
          "name": "provider",
          "type": "string"
      },
      {
          "internalType": "string",
          "name": "organizationName",
          "type": "string"
      },
      {
          "internalType": "string",
          "name": "userName",
          "type": "string"
      },
      {
          "internalType": "uint256",
          "name": "timestamp",
          "type": "uint256"
      }
  ],
  "stateMutability": "view",
  "type": "function"
},
{
  "inputs": [{
      "internalType": "uint256",
      "name": "_id",
      "type": "uint256"
  }],
  "name": "getContent",
  "outputs": [{
      "components": [{
              "internalType": "uint256",
              "name": "id",
              "type": "uint256"
          },
          {
              "internalType": "address",
              "name": "user",
              "type": "address"
          },
          {
              "internalType": "string",
              "name": "name",
              "type": "string"
          },
          {
              "internalType": "string",
              "name": "createdAt",
              "type": "string"
          },
          {
              "internalType": "string",
              "name": "link",
              "type": "string"
          },
          {
              "internalType": "string",
              "name": "enrollmentPrice",
              "type": "string"
          },
          {
              "internalType": "string",
              "name": "place",
              "type": "string"
          },
          {
              "internalType": "string",
              "name": "contentType",
              "type": "string"
          },
          {
              "internalType": "string",
              "name": "provider",
              "type": "string"
          },
          {
              "internalType": "string",
              "name": "organizationName",
              "type": "string"
          },
          {
              "internalType": "string",
              "name": "userName",
              "type": "string"
          },
          {
              "internalType": "uint256",
              "name": "timestamp",
              "type": "uint256"
          }
      ],
      "internalType": "struct ContentVerification.Content",
      "name": "",
      "type": "tuple"
  }],
  "stateMutability": "view",
  "type": "function"
}
]
const CONTRACT_ADDRESS = '0x5faC1Aa6AF8e5d510cb6D971E87F0a2C57C2E992' // ganti sendiri

// Fungsi untuk memanggil addContent
export async function addContentSmartContract(
  _name,
  _createdAt,
  _link,
  _enrollmentPrice,
  _place,
  _contentType,
  _provider,
  _organizationName,
  _userName
) {
  try {
    const walletProvider = appKit.getWalletProvider()
    if (!walletProvider) {
      throw new Error('Wallet belum terkoneksi.')
    }
    const ethersProvider = new BrowserProvider(walletProvider)
    const signer = await ethersProvider.getSigner()
    
    const contract = new Contract(CONTRACT_ADDRESS, contractABI, signer)
    const tx = await contract.addContent(
      _name,
      _createdAt,
      _link,
      _enrollmentPrice,
      _place,
      _contentType,
      _provider,
      _organizationName,
      _userName
    )
    console.log('Tx addContent terkirim: ', tx.hash)
    const receipt = await tx.wait()
    console.log('Tx success, receipt:', receipt)

    return receipt
  } catch (err) {
    console.error(err)
    throw err
  }
}
