package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.PostVO;

	public class PostService extends AbstractService
	{
		public function PostService()
		{
			super("PostService", "http://aerial-test/server.php", PostVO);
		}
	}
}