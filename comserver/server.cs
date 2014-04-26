//http://tech.pro/tutorial/704/csharp-tutorial-simple-threaded-tcp-server
/* This server is designed to continuosly listen to a TCP destination for data to be broadcasted
 * alternative design is to have system listen for TCP and have access to com port
 * that will essentially interupt the transmission to upload to serveer and then continue with 
 * broadcast of com information to TCP
 *
 */

using System;
using System.Text;
using System.Net.Sockets;
using System.Threading;
using System.Net;

namespace TCPServerTutorial
{
  class Server
  {
    private TcpListener tcpListener;
    private Thread listenThread;

    public Server()
    {
      this.tcpListener = new TcpListener(IPAddress.Any, 3000);
      this.listenThread = new Thread(new ThreadStart(ListenForClients));
      this.listenThread.Start();
    }
  }
}
