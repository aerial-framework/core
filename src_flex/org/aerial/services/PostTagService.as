package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.PostTagVO;

	public class PostTagService extends AbstractService
	{
		public function PostTagService()
		{
			super("PostTagService", "http://aerial-test/server.php", PostTagVO);
		}
	}
}